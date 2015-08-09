<?php

namespace PE\Encoders;

use LSS\Array2XML;
use PE\Encoder;
use PE\Exceptions\EncoderException;
use PE\Nodes\EncoderNode;
use PE\Nodes\EncoderNodeChild;
use PE\Options\EncoderOptions;

class XmlEncoder extends Encoder {

	const ROOT_NODE_NAME = 'tb';

	public function encode($object, EncoderOptions $options = null) {
		$arr = parent::encode($object, $options);
		return Array2XML::createXML(self::ROOT_NODE_NAME, $arr['processed']);
	}
	protected function encodeNodeChildren(EncoderNode $node, $nodeName, EncoderNodeChild $child, $children) {
		if (!$node->isSingleNode($nodeName)) {
			return array($node->getNodeNameSingle() => $children);
		}
		return $children;
	}
	protected function encodeAttributes($array) {
		$attributes['@attributes'] = $array;
		return $attributes;
	}




	public function decode($node, EncoderOptions $options = null) {
		if (!$options) {
			$options = new EncoderOptions(array());
		}
		if (!$options->hasOption('keyCamelCase')) {
			$options->setOptions(array('keyCamelCase' => true));
		}
		return parent::decode($node, $options);
	}
	protected function decodeChildNames($structure) {
		$names = array();
		foreach ($structure->children() as $child) {
			$names[] = $child->getName();
		}
		return $names;
	}
	protected function decodeRawNode($nodeData, EncoderNode $nodeProxy, $isSingle) {
		$path = $nodeProxy->getNodeNameSingle();
		if (!$isSingle) {
			$path = $nodeProxy->getNodeName() . '/' . $path;
		}
		$children = $nodeData->xpath($path);
		if ($isSingle && count($children) == 0) {
			throw new EncoderException(sprintf('There are no children found in node %s', ($isSingle ? $nodeProxy->getNodeNameSingle() : $nodeProxy->getNodeName())));
		}

		return ($isSingle ? $children[0] : $children);
	}
	protected function decodeNode($nodeData) {
		$nodeData = (array) $nodeData;
		$attributes = array();
		if (isset($nodeData['@attributes'])) {
			$attributes = $nodeData['@attributes'];
		}
		unset($nodeData['@attributes']);
		return array_merge($attributes, $nodeData);
	}
} 