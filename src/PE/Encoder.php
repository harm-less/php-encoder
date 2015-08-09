<?php

namespace PE;

use PE\Interfaces\IEncoder;
use PE\Nodes\EncoderNode;
use PE\Nodes\EncoderNodeChild;
use PE\Nodes\EncoderNodeVariable;
use PE\Library\Inflector;

use PE\Enums\ActionVariable;
use PE\Exceptions\EncoderException;
use PE\Options\EncoderOptions;

class Encoder implements IEncoder {



	public function decode($node, EncoderOptions $options = null) {
		$arr = array();
		$childNodeNames = $this->decodeChildNames($node);
		$options = $options ? $options : new EncoderOptions(null);

		foreach ($childNodeNames as $childNodeName) {
			$nodeArray = $this->decodeRawToArray($childNodeName, $node, array());
			$arr[$childNodeName] = $this->_decodeNode($childNodeName, array($childNodeName => $nodeArray), $options);
		}
		return $arr;
	}

	protected function decodeRawToArray($nodeName, $node, $array) {

		$proxyNode = EncoderNode::getNode($nodeName);
		$isSingleNode = EncoderNode::isSingleNode($nodeName);
		$nodeChildrenData = $this->decodeRawNode($node, $proxyNode, $isSingleNode);

		if ($isSingleNode) {
			$nodeChildrenData = array($nodeChildrenData);
		}

		$childrenArr = array();
		foreach ($nodeChildrenData as $nodeChildData) {
			$decodedData = $this->decodeNode($nodeChildData);

			$nodeChildType = array_key_exists('type', $decodedData) ? $decodedData['type'] : null;
			$nodeTypeStr = ($nodeChildType !== null && !empty($nodeChildType) ? $nodeChildType : $proxyNode->getDefaultType());

			$nodeType = $proxyNode->getType($nodeTypeStr);
			if ($nodeType === null) {
				throw new EncoderException(sprintf('Can\'t find proxy type "%s". Make sure it has been loaded.', $nodeTypeStr));
			}

			$childArr = array();

			foreach ($decodedData as $childName => $childData) {
				if ($nodeType->isChild($childName)) {
					$childArr[$childName] = $this->decodeRawToArray($childName, $nodeChildData, $array);
				}
				else {
					if (is_array($childData)) {
						throw new EncoderException(sprintf('Trying to decode node, but child node "%s" doesn\'t seem to be configured or you are trying to save an array into an attribute which is illegal (imploded value: "%s"). Make sure you either register this node or to encode the value to something other than an array.', $childName, implode(', ', $childData)));
					}
					$childArr[$childName] = $childData;
				}
			}

			array_push($childrenArr, $childArr);
		}

		if ($isSingleNode) {
			return $childrenArr[0];
		}

		return $childrenArr;
	}

    protected function decodeRawNode($nodeData, EncoderNode $nodeProxy, $isSingle) {
        $nodeData = (array) $nodeData;
        $arr = (array) ($isSingle ? $nodeData[$nodeProxy->getNodeNameSingle()] : $nodeData[$nodeProxy->getNodeName()]);
        return $arr;
    }
	protected function decodeNode($node) {
        return (array) $node;
	}
    protected function decodeChildNames($structure) {
        return array_keys((array)$structure);
    }

	protected function _decodeNode($nodeName, $nodeData, EncoderOptions $options, EncoderNode $parentNode = null, $parentObject = null, $parentNodeData = null) {

		if (!EncoderNode::nodeExists($nodeName)) {
			throw new EncoderException(sprintf('Node name "%s" is not specified', $nodeName));
		}
		$proxyNode = EncoderNode::getNode($nodeName);
		$isSingleNode = EncoderNode::isSingleNode($nodeName);

		$nodeDataChild = $nodeData[$nodeName];

		if ($isSingleNode) {
			$nodeDataChild = array($nodeDataChild);
		}

		$childNode = null;
		$addAfterDecode = true;
		$addAfterAttributes = true;
		if ($parentNode) {
			$childNode = $parentNode->getChild($nodeName);
			$addAfterDecode = $childNode->setAfterChildren();
			$addAfterAttributes = $childNode->setAfterAttributes();
		}

		$objects = array();
		$decodedChildren = array();

		$nodeIndex = 0;
		foreach ($nodeDataChild as $nodeDataItem) {

			$nodeChildType = $proxyNode->getObjectType($parentObject, $nodeDataItem);
			$nodeType = ($nodeChildType !== null && !empty($nodeChildType) ? $nodeChildType : $proxyNode->getDefaultType());

			foreach ($nodeDataItem as $nodeDataName => $nodeDataValue) {
				$nodeDataNameDashed = Inflector::underscore($nodeDataName, '-');
				if ($nodeDataName !== $nodeDataNameDashed) {
					$nodeDataItem[$nodeDataNameDashed] = $nodeDataValue;
					unset($nodeDataItem[$nodeDataName]);
				}
			}

			$type = $proxyNode->getType($nodeType);
			if ($type === null) {
				throw new EncoderException(sprintf('Proxy node type "%s" in node "%s" could not be found. Presumably is has not been loaded', $nodeType, $nodeName));
			}

			// execute variables that are required to execute before the object is made
			$alwaysExecutedVariables = $type->getAlwaysExecutedVariables();
			foreach ($alwaysExecutedVariables as $variableId => $variable) {
				if (!array_key_exists($variableId, $nodeDataItem)) {
					$nodeDataItem[$variableId] = $variable->getDefaultValue();
				}
			}

			// call node methods
			$nodeMethodVariables = $type->getVariablesSetterActionByType(EncoderNodeVariable::ACTION_TYPE_NODE);
			foreach ($nodeMethodVariables as $variableId => $nodeMethodVariable) {
				if (isset($nodeDataItem[$variableId])) {
					$setterOptions = array(
						ActionVariable::SETTER_NAME => $variableId,
						ActionVariable::SETTER_PARENT => $parentObject,
						ActionVariable::SETTER_VALUE => $nodeDataItem[$variableId]
					);
					if ($newNode = $nodeMethodVariable->callNodeSetterAction($type, $nodeDataItem, $setterOptions)) {
						$nodeDataItem = $newNode;
					}
				}
			}

			// add the full decoded node data to a node array
			$decodedChildren[] = $nodeDataItem;

			// if the node needs to create a new object
			if ($type->needsObject()) {

				$nodeClassName = $type->getObjectClassName();

				// load the class object this node should decode into
				if (!class_exists($nodeClassName)) {
					$type->loadObject();
				}

				$requiredVariables = $this->getRequiredConstructorVariables($nodeClassName);
				$requiredVariableValues = array();
				foreach ($requiredVariables as $variable) {

					$processedVariable = $variable;
					if ($options->option('keyCamelCase') === true) {
						$processedVariable = Inflector::underscore($variable, '-');
					}

					if (!array_key_exists($processedVariable, $nodeDataItem)) {
						throw new EncoderException(sprintf('Variable "%s" for "%s" does not exist but is required to create an object for node "%s" (Node type: "%s") at index "%s"', $processedVariable, $nodeClassName, $nodeName, $type->getNodeName(), $nodeIndex));
					}
					$requiredValue = $nodeDataItem[$processedVariable];
					$processedRequiredValue = $type->processValue($processedVariable, $requiredValue);
					if ($processedRequiredValue === null) {
						throw new EncoderException(sprintf('Variable "%s" for "%s" cannot process its value (%s). Presumably because the NodeType does not recognize the variable', $processedVariable, $nodeClassName, $requiredValue));
					}

					array_push($requiredVariableValues, $processedRequiredValue);
					unset($nodeDataItem[$processedVariable]);
				}

				// create a new instance of the class
				$rc = new \ReflectionClass($nodeClassName);
				$nodeInstance = $rc->newInstanceArgs($requiredVariableValues);

				// get the key of the object, but if it isn't set or is empty, use the index. Afterwards, add it to a children array
				$typeKey = $type->getKey();
				$objects[$nodeIndex] = $nodeInstance;

				if (!$addAfterDecode && !$addAfterAttributes) {
					$parentNode->addChildrenToObject($nodeName, $parentObject, array($nodeInstance));
				}

				foreach ($nodeDataItem as $name => $value) {
					$type->setToObject($parentObject, $nodeDataItem, $nodeInstance, $name, $value);
				}

				if (!$addAfterDecode && $addAfterAttributes) {
					$parentNode->addChildrenToObject($nodeName, $parentObject, array($nodeInstance));
				}

				// set node children if they exist
				foreach ($nodeDataItem as $childName => $value) {
					if ($type->isChild($childName)) {
						$children = $this->_decodeNode($childName, $nodeDataItem, $options, $type, $nodeInstance, $nodeDataItem);

						$type->addChildrenToObject($childName, $nodeInstance, $children);
					}
				}
			}

			$nodeIndex++;
		}
		// this broke, perhaps this can be fixed some time
		//$proxyNode->variablesAreValid($decodedChildren);

		if ($isSingleNode) {
			foreach ($objects as $object) {
				return $object;
			}
		}

		return $objects;
	}

	protected function getRequiredConstructorVariables($className) {
		$reflector = new \ReflectionMethod($className, '__construct');
		$requiredVariables = $reflector->getParameters();

		$arr = array();
		foreach ($requiredVariables as $variable) {
			if (!$variable->isOptional()) {
				array_push($arr, $variable->getName());
			}
		}
		return $arr;
	}


	public function encode($object, EncoderOptions $options = null) {
		$node = EncoderNode::getNodeTypeByObject($object);
		if ($node) {
			$encodedArray = $this->_encode($object, $node, ($options ? $options : new EncoderOptions(null)));
			$processedArray = &$encodedArray['processed'];

			$objectNode = EncoderNode::getNodeTypeByObject($object);

			$wrapperName = $objectNode->getNodeNameSingle();
			if ($options) {
				if ($options->hasOption('wrapper')) {
					$wrapperName = $options->option('wrapper');
				}
			}
			if ($wrapperName !== false) {
				$processedArray = array($wrapperName => $processedArray);
			}
			return $encodedArray;
		}
		return null;
	}

	protected function _encode($object, EncoderNode $node, EncoderOptions $options, $parent = null, $nodeIterationIndex = null, $childObjectIterationIndex = null) {
		$variables = $node->getVariables();

        $optionNodeIndex = $node->getNodeName() . '[' . $childObjectIterationIndex . ']';

		$attributesRaw = array();
		foreach ($variables as $variable) {
			if (!$variable->hasGetterAction() || $variable->alwaysExecute()) {

				$variableId = $variable->getId();

                $attributeValue = null;
				$getAttributeMethod = $variable->getGetterMethod();
				if (method_exists($object, $getAttributeMethod)) {
					$attributeValue = $object->$getAttributeMethod();
				} else {
					//throw new ProxyException(sprintf('Getter method "%s" does not exist in object "%s" for node type "%s" (%s) and variable with id "%s".', $getAttributeMethod, get_class($object), $node->getTypeName(), get_class($node), $variableId));
				}

				$attributesRaw[$variableId] = $attributeValue;
			}
		}

		/*// execute variables that are required to execute before the object is made
		$alwaysExecutedVariables = $node->getAlwaysExecutedVariables();
		foreach ($alwaysExecutedVariables as $variableId => $variable) {
			if (!array_key_exists($variableId, $nodeData)) {
				$nodeData[$variableId] = $variable->getDefaultValue();
			}
		}*/

		$actionVariables = array(
			ActionVariable::GETTER_OBJECT => $object,
			ActionVariable::GETTER_PARENT => $parent,
			ActionVariable::GETTER_OPTIONS => $options,
			ActionVariable::GETTER_NODE_ITERATION_INDEX => $nodeIterationIndex,
			ActionVariable::GETTER_CHILD_OBJECT_ITERATION_INDEX => $childObjectIterationIndex,
		);

		$nodeMethodVariables = $node->getVariablesGetterActionByType(EncoderNodeVariable::ACTION_TYPE_NODE);
		$attributesRaw = $this->loopNodeVariables($node, $nodeMethodVariables, $attributesRaw, $actionVariables);

		$optionIteration = $options->option('iterate', $node, $nodeIterationIndex);
        $optionNodeKey = $options->option('key', $node);
        $optionNodeValue = $options->option('value', $node);
        $optionEncode = $options->option('encode', $node);
        $optionEncodeAttributes = $options->option('attributes', $node);
        $optionEncodeChildren = $options->option('children', $node);

        $encodeChildren = true;
        $encodeAttributes = true;

        if (is_bool($optionEncode)) {
            $encodeAttributes = $optionEncode;
            $encodeChildren = $optionEncode;
        }
        if (is_bool($optionEncodeAttributes)) {
            $encodeAttributes = $optionEncodeAttributes;
        }
        if (is_bool($optionEncodeChildren)) {
            $encodeChildren = $optionEncodeChildren;
        }
        if ($optionNodeValue !== null || $optionNodeKey !== null) {
            $encodeAttributes = false;
        }

		$nodesRaw = array();
		$childrenProcessed = array();

		$children = $node->getChildren();
		foreach ($children as $childNodeName => $child) {

			if (!EncoderNode::nodeExists($child->getNodeName())) {
				throw new EncoderException(sprintf('Cannot set the node name (%s) of a proxy node child because it doesn\'t exist. Please add the requested node with "ProxyNode::addNode()". Current node name "%s" with class name "%s"', $child->getNodeName(), $node->getNodeName(), get_class($node)));
			}

            $childOptionPath = $optionNodeIndex . ':' . $childNodeName;
			$optionChildIteration = $options->option('iterate', $childOptionPath);
            $optionChildKey = $options->option('key', $childOptionPath);

            $isIterated = $optionChildIteration !== null;
            $childIteration = $optionChildIteration === null ? 1 : $optionChildIteration;

			$getChildObjectsMethod = $child->getGetterMethod();
			if (!method_exists($object, $getChildObjectsMethod)) {
				throw new EncoderException(sprintf('Getter method "%s" for node "%s" does not exist in class "%s"', $getChildObjectsMethod, $childNodeName, get_class($object)));
			}
			$childObjects = $object->$getChildObjectsMethod();

			if (!$child->isArray()) {
				$childObjects = array($childObjects);
			}
			else if (!is_array($childObjects)) {
				throw new EncoderException(sprintf('Children object for node "%s" must be an array and this can be determined by the parameter "isArray" in ', $childNodeName));
			}

			$rawChildrenInformationIteration = array();

			$nodeIteration = 0;
			for($i = 0; $i < $childIteration; $i++) {

				$childObjectIteration = 0;
				foreach ($childObjects as $childObject) {
					$childNodeType = EncoderNode::getNodeTypeByObject($childObject);

					if ($childNodeType === null) {
						throw new EncoderException(sprintf('Child node type for object "%s (child of "%s")" for node "%s" not found', get_class($childObject), $node->getNodeName(), $childNodeName));
					}

					$childNodeData = $this->_encode($childObject, $childNodeType, $options, $object, $nodeIteration, $childObjectIteration);

					$rawChildNode = $childNodeData['raw'];
					$rawChildNodeAttributes = $rawChildNode['attributes'];
					$rawChildNodeChildren = $rawChildNode['children'];

					$processedChildNode = $childNodeData['processed'];

					$rawChildrenInformationIteration[$nodeIteration][$childObjectIteration] = array('attributes' => $rawChildNodeAttributes, 'children' => $rawChildNodeChildren, 'nodeName' => $childNodeName);

					if ($optionChildKey !== null) {
						if ($isIterated) {
							$childrenProcessed[$nodeIteration][$rawChildNodeAttributes[$optionChildKey]] = $processedChildNode;
						}
						else {
							$childrenProcessed[$rawChildNodeAttributes[$optionChildKey]] = $processedChildNode;
						}
					}
					else {
						if (!isset($childrenProcessed[$childNodeName])) {
							$childrenProcessed[$childNodeName] = array();
						}
						$childrenProcessedTemp = array($childObjectIteration => $processedChildNode);
						if ($isIterated) {
							$childrenProcessed[$childNodeName] = array_merge_recursive($childrenProcessed[$childNodeName], $this->encodeNodeChildren($childNodeType, $childNodeName, $child, array($nodeIteration => $childrenProcessedTemp)));
						}
						else {
							$childrenProcessed[$childNodeName] = array_merge_recursive($childrenProcessed[$childNodeName], $this->encodeNodeChildren($childNodeType, $childNodeName, $child, $childrenProcessedTemp));
						}
					}

					$childObjectIteration++;
				}

				$nodeIteration++;
			}

			if (count($rawChildrenInformationIteration)) {
				$rawIteratedChildren = ($isIterated ? $rawChildrenInformationIteration : $rawChildrenInformationIteration[0]);
				$nodesRaw[$childNodeName] = $rawIteratedChildren;
			}
		}

		$attributesProcessed = $this->encodeAttributes($attributesRaw);
		if (!$encodeAttributes) {
			$attributesProcessed = array();
		}

		if ($options->option('keyCamelCase', $node) === true) {
			$attributesCamelCased = array();
			foreach($attributesProcessed as $attrKey => $attrValue) {
				$attributesCamelCased[Inflector::camelize($attrKey, true, '-')] = $attrValue;
			}
			$attributesProcessed = $attributesCamelCased;
		}

		$nodeData = array_merge($attributesProcessed, $childrenProcessed);

		if ($optionNodeValue !== null) {
			if (!array_key_exists($optionNodeValue, $attributesRaw)) {
				throw new EncoderException(sprintf('Option "value" cannot be mapped to "%s" because it does not exist in "%s"', $optionNodeValue, $node->getNodeName()));
			}
			$nodeData = $attributesRaw[$optionNodeValue];
		}

		return array(
			'processed' => $nodeData,
			'raw' => array(
				'attributes' => $attributesRaw,
				'children' => $nodesRaw,
				'nodeName' => $node->getNodeName()
			)
		);
	}

	protected function encodeNodeChildren(EncoderNode $node, $nodeName, EncoderNodeChild $child, $objects) {

		$childrenTemp = $objects;
		if (!$child->isArray()) {
			$childrenTemp = $childrenTemp[0];
		}
		return $childrenTemp;
	}


	protected function encodeAttributes($attributes) {
		return $attributes;
	}


	protected function loopNodeVariables($node, $variables, $data, $actionOptions) {
		$temp = $data;
		foreach ($variables as $variableId => $nodeMethodVariable) {
			$hasVariable = array_key_exists($variableId, $data);
			if ($hasVariable || $nodeMethodVariable->alwaysExecute()) {
				$actionOptions = array_merge(array(
					ActionVariable::GETTER_NAME => $variableId,
					ActionVariable::GETTER_VALUE => $hasVariable ? $data[$variableId] : null,
				), $actionOptions);
				if ($newAttributeData = $nodeMethodVariable->callNodeGetterAction($node, $data, $actionOptions)) {
					if (is_array($newAttributeData)) {
						$temp = $newAttributeData;
					}
				}
			}
		}
		return $temp;
	}



	protected function errorNoTemplate($extra = null) {
		throw new EncoderException("No template has been found in the structure." . ($extra ? ' ' . $extra : ''));
	}
}