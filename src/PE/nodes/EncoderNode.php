<?php

namespace PE\Nodes;

use ReflectionClass;
use PE\Exceptions\EncoderNodeException;
use PE\Variables\Variable;

class EncoderNode {

	/**
	 * @var EncoderNode[]
	 */
	private static $nodes = array();
	/**
	 * @var EncoderNode[]
	 */
	private static $nodesTypes = array();

	/**
	 * @var EncoderNode[]
	 */
	private static $getNodeTypeByObjectCache = array();

	private $_nodeIsObjectCache;

	private $nodeOptions;

	private $children;

	/**
	 * @var EncoderNodeVariableCollection
	 */
	private $variables;
	private $key;

	private $nodeName;
	private $nodeNameSingle;
	private $isolatedNodeName;
	public $typeName;

	private $needsClass;
	private $classPrepend;

	const DEFAULT_TYPE = 'default';

	const NODE_EXTENSION = 'Node';

	function __construct($nodeName, $nodeNameSingle, $classPrepend) {

		$this->nodeClasses = array();
		$this->_nodeIsObjectCache = array();

		$this->children = new EncoderNodeChildren();
		$this->nodeOptions = new Variable();
		$this->variables = new EncoderNodeVariableCollection();

		$this->setNodeName($nodeName, $nodeNameSingle);

		$this->needsObject(true);
		$this->classPrepend = $classPrepend;
	}

	public static function addNode(EncoderNode $node) {
		$nodeName = $node->getNodeName();
		$nodeNameSingle = $node->getNodeNameSingle();
		if ($nodeName == null) {
			throw new EncoderNodeException('Node without a name has been added');
		}
		if ($nodeName != null && self::nodeExists($nodeName)) {
			return false;
		}
		if ($nodeNameSingle != null && self::nodeExists($nodeName)) {
			return false;
		}
		self::$nodes[$nodeName] = self::$nodes[$nodeNameSingle] = $node;

		// make this node the default one if no type has yet been specified
		if (count(self::getNodeTypes($nodeName)) == 0) {
			self::addNodeType($node, self::DEFAULT_TYPE);
		}
		return true;
	}

	public static function isSingleNode($nodeName) {
		$node = self::getNode($nodeName);
		return $node && $node->getNodeNameSingle() == $nodeName;
	}

	/**
	 * @param string $nodeName
	 * @return EncoderNode
	 */
	public static function getNode($nodeName) {
		if (isset(self::$nodes[$nodeName])) {
			return self::$nodes[$nodeName];
		}
		return null;
	}

	/**
	 * @param object $object
	 * @return EncoderNode
	 */
	public static function getNodeByObject($object) {
		foreach (self::getNodes() as $node) {
			if ($node->nodeIsObject($object)) {
				return $node;
			}
		}
		return null;
	}
	public static function nodeExists($nodeName) {
		return self::getNode($nodeName) != null;
	}

	public static function getNodes() {
		return self::$nodes;
	}

	public static function getNodeTypeId($nodeName, $nodeType) {
		return strtolower($nodeName) . ':' . lcfirst($nodeType);
	}
	public static function addNodeType(EncoderNode $nodeType, $nodeTypeName) {

		if (self::nodeTypeExists($nodeType->getNodeName(), $nodeTypeName)) {
			return false;
		}
		$nodeType->typeName = $nodeTypeName;
		$nodeTypeId = self::getNodeTypeId($nodeType->getNodeName(), $nodeTypeName);
		self::$nodesTypes[$nodeTypeId] = $nodeType;
		return true;
	}

	public static function getNodeTypeByObject($object) {
		$cache = self::$getNodeTypeByObjectCache;
		$className = get_class($object);
		if (isset($cache[$className])) {
			return $cache[$className];
		}
		foreach (self::getNodeTypes() as $node) {
			if ($node->nodeIsObject($object)) {
				self::$getNodeTypeByObjectCache[$className] = $node;
				return $node;
			}
		}
		self::$getNodeTypeByObjectCache[$className] = null;
		return null;
	}

	/**
	 * @param string $nodeName
	 * @return EncoderNode[]
	 */
	public static function getNodeTypes($nodeName = null) {
		if ($nodeName === null) {
			return self::$nodesTypes;
		}
		else {
			$types = array();
			foreach (self::getNodeTypes() as $nodeTypeId => $nodeType) {
				$hashId = explode(':', $nodeTypeId);
				$nodeNameExt = $hashId[0];
				$typeNameExt = $hashId[1];
				if ($nodeNameExt === $nodeName) {
					$types[$typeNameExt] = $nodeType;
				}
			}
			return $types;
		}
	}


	public static function getNodeType($nodeName, $nodeType = EncoderNode::DEFAULT_TYPE) {
		$nodeTypeId = self::getNodeTypeId($nodeName, $nodeType);
		if (isset(self::$nodesTypes[$nodeTypeId])) {
			return self::$nodesTypes[$nodeTypeId];
		}
		return null;
	}
	public static function nodeTypeExists($nodeName, $nodeType) {
		return self::getNodeType($nodeName, $nodeType) != null;
	}



	public function classPrepend() {
		return $this->classPrepend;
	}

	public function getNodeObjectName() {
		return $this->_getNodeIsolatedClassName();
	}

	private function _getNodeIsolatedClassName() {
		if ($this->isolatedNodeName !== null) {
			return $this->isolatedNodeName;
		}

		$objectClass = new ReflectionClass($this);
		$objectClassShortName = $objectClass->getShortName();
		$objectClassShortNameStripped = preg_replace('/' . EncoderNode::NODE_EXTENSION . '$/', '', $objectClassShortName);

		$this->isolatedNodeName = $objectClassShortNameStripped;
		return $this->isolatedNodeName;
	}

	public function nodeIsObject($object) {
		$className = get_class($object);
		if (isset($this->_nodeIsObjectCache[$className])) {
			return $this->_nodeIsObjectCache[$className];
		}
		$objectClass = new ReflectionClass($object);
		$objectClassShortName = $objectClass->getShortName();
		$nodeIsObject = $objectClassShortName === $this->getNodeObjectName();
		$this->_nodeIsObjectCache[$className] = $nodeIsObject;
		return $nodeIsObject;
	}
	public function objectIsNodeObject($object) {
		$objectClass = new ReflectionClass($object);
		$objectClassShortName = $objectClass->getShortName();
		return $objectClassShortName === $this->getNodeObjectName();
	}

	protected function setNodeName($nodeName, $nodeNameSingle) {
		$this->nodeName = $nodeName;
		$this->nodeNameSingle = $nodeNameSingle;
	}
	public function getNodeName() {
		return $this->nodeName;
	}
	public function getNodeNameSingle() {
		return $this->nodeNameSingle;
	}

	public function getTypeName() {
		return $this->typeName;
	}

	public function addOptions($options) {
		$this->nodeOptions->parseOptions($options);
	}

	public function getDefaultType() {
		return EncoderNode::DEFAULT_TYPE;
	}
	public function needsObject($bool = null) {
		if ($bool !== null && is_bool($bool)) {
			$this->needsClass = $bool;
		}
		return $this->needsClass;
	}




	public function addChildNode(EncoderNodeChild $child) {
		return $this->children->addChild($child);
	}
	public function getChild($childNodeName) {
		return $this->children->getChild($childNodeName);
	}
	/**
	 * @return EncoderNodeChild[]
	 */
	public function getChildren() {
		return $this->children->getChildren();
	}
	public function childNodeExists($nodeName) {
		return $this->children->childExists($nodeName);
	}
	public function isChild($nodeName) {
		return $this->children->isChild($nodeName);
	}
	public function addChildrenToObject($childName, $target, $values) {
		return $this->children->addChildrenToObject($childName, $target, $values);
	}




	public function loadPlugin($pluginName) {
		throw new EncoderNodeException('Must be overwritten by subclasses');
	}

	public function type() {
		return $this->typeName;
	}

	/**
	 * @param string $type
	 * @return EncoderNode
	 */
	public function getType($type) {
		return EncoderNode::getNodeType($this->getNodeName(), $type);
	}

	/**
	 * @param $type
	 * @param EncoderNode $typeObject
	 *
	 * @return bool
	 */
	public function addType($type, EncoderNode $typeObject = null) {

		if ($typeObject == null) {

			$typeClassName = $this->getTypeClassName($type) . EncoderNode::NODE_EXTENSION;

			// loads the class if it has need bee loaded
			if (!class_exists($typeClassName)) {
				$this->loadType($type);
			}

			// check if it has been loaded an set the object, otherwise return
			if (class_exists($typeClassName)) {
				$typeObject = new $typeClassName();
			}
			else {
				return false;
			}
		}

		return EncoderNode::addNodeType($typeObject, $type);
	}




	protected function _loadObject($object) {
		return null;
	}

	protected function _objectClassName() {
		return $this->classPrepend() . '\\' . $this->_objectFileName();
	}

	protected function _objectFileName() {
		return $this->_getNodeIsolatedClassName();
	}
	public function loadObject($object = null) {
		if ($object === null && $object = $this->_objectFileName()) {
			if ($object === null) {
				throw new EncoderNodeException('Object cannot be null');
			}
		}
		return $this->_loadObject($object);
	}
	public function getObjectClassName() {
		return $this->_objectClassName();
	}




	public function setToObject($parent, $nodeData, $object, $name, $value) {
		$variable = $this->getVariable($name);
		if ($variable == null) {
			return false;
		}
		return $variable->setToObject($this, $nodeData, $parent, $object, $value);
	}

	public function addVariable(EncoderNodeVariable $variable) {
		return $this->variables->addNodeVariable($variable);
	}
	public function alterVariable($variable, $options) {
		return $this->variables->alterVariable($variable, $options);
	}
	public function getVariable($variable) {
		return $this->variables->getVariable($variable);
	}
	public function getVariableById($id) {
		return $this->variables->getVariableById($id);
	}
	public function getVariableByName($name) {
		return $this->variables->getVariableByName($name);
	}
	public function getVariablesSetterActionByType($type) {
		return $this->variables->getVariablesSetterActionByType($type);
	}
	public function getVariablesGetterActionByType($type) {
		return $this->variables->getVariablesGetterActionByType($type);
	}

	/**
	 * @return EncoderNodeVariable[]
	 */
	public function getAlwaysExecutedVariables() {
		return $this->variables->getAlwaysExecutedVariables();
	}

	/**
	 * @param bool $order
	 * @return EncoderNodeVariable[]
	 * @throws \Exception
	 */
	public function getVariables($order = true) {
		return $this->variables->getVariables($order);
	}
	public function variableExists($id) {
		return $this->variables->variableExists($id);
	}
	public function variablesAreValid($nodeDataArray) {
		return $this->variables->variablesAreValidWithData($nodeDataArray);
	}
	public function processValue($name, $value) {
		return $this->variables->processValue($name, $value);
	}

	public function key($variableId) {
		$this->key = $variableId;
	}
	public function getKey() {
		return $this->key;
	}

	public function getObjectType($parent, $nodeData) {
		return isset($nodeData['type']) ? $nodeData['type'] : null;
	}
}