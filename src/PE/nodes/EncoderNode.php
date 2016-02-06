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
	private static $getNodeByObjectCache = array();
	private static $getNodeTypeByObjectCache = array();

	private $_nodeIsObjectCache;

	private $nodeOptions;

	/**
	 * @var EncoderNodeChildren
	 */
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

	/**
	 * Adds a node
	 *
	 * This method is essential for using this library because it will instruct it what to do and how to do it.
	 *
	 * @param EncoderNode $node
	 * @return bool Will return true if the node had been successfully added
	 */
	public static function addNode(EncoderNode $node) {
		$nodeName = $node->getNodeName();
		$nodeNameSingle = $node->getNodeNameSingle();
		if ($nodeName === null || empty($nodeName) || !is_string($nodeName)) {
			throw new EncoderNodeException('Node without a name has been added. It must be a string and it cannot be empty.');
		}
		if (self::nodeExists($nodeName)) {
			throw new EncoderNodeException(sprintf('Node with name "%s" already exists', $nodeName));
		}
		else if ($nodeNameSingle != null) {
			if (self::nodeExists($nodeNameSingle)) {
				throw new EncoderNodeException(sprintf('Node with single name "%s" already exists', $nodeNameSingle));
			}
			self::$nodes[$nodeNameSingle] = $node;
		}
		self::$nodes[$nodeName] = $node;

		// make this node the default one if no type has yet been specified
		if (count(self::getNodeTypes($nodeName)) == 0) {
			self::addNodeType($node, self::DEFAULT_TYPE);
		}

		self::softCleanNodeCache();
		return true;
	}

	/**
	 * @param string $nodeName
	 * @return EncoderNode|null Returns the EncoderNode if found. If no node is found it returns null
	 */
	public static function getNode($nodeName) {
		if (isset(self::$nodes[$nodeName])) {
			return self::$nodes[$nodeName];
		}
		return null;
	}

	/**
	 * @param object $object
	 * @return EncoderNode|null Returns a node if the object matches any of the existing nodes. Returns null if no nodes are found
	 */
	public static function getNodeByObject($object) {
		$cache = self::$getNodeByObjectCache;
		$className = get_class($object);
		if (isset($cache[$className])) {
			return $cache[$className];
		}
		$nodeTemp = null;
		foreach (self::getNodes() as $node) {
			if ($node->nodeIsObject($object)) {
				$nodeTemp = $node;
			}
		}
		self::$getNodeByObjectCache[$className] = $nodeTemp;
		return $nodeTemp;
	}

	/**
	 * @return EncoderNode[] Returns a list of all the registered nodes
	 */
	public static function getNodes() {
		return self::$nodes;
	}

	/**
	 * @param string $nodeName
	 * @return bool Returns true if the node name is a single one
	 */
	public static function isSingleNode($nodeName) {
		$node = self::getNode($nodeName);
		return $node && $node->getNodeNameSingle() === $nodeName;
	}

	/**
	 * @param string $nodeName
	 * @return bool Returns true if the node exists
	 */
	public static function nodeExists($nodeName) {
		return self::getNode($nodeName) != null;
	}

	/**
	 * Adds a node type
	 *
	 * Adding a node type will give the ability to a node to have different kind of properties based on the original
	 * node. You can compare it with inheriting functions from a parent class.
	 *
	 * @param EncoderNode $nodeType
	 * @param string $nodeTypeName
	 * @return bool Returns true if the node type was successfully added
	 */
	public static function addNodeType(EncoderNode $nodeType, $nodeTypeName) {
		$nodeName = $nodeType->getNodeName();
		if (self::nodeTypeExists($nodeName, $nodeTypeName)) {
			throw new EncoderNodeException(sprintf('Node type with name "%s" and node type name "%s" already exists', $nodeName, $nodeTypeName));
		}
		$nodeType->typeName = $nodeTypeName;
		$nodeTypeId = self::getNodeTypeId($nodeType->getNodeName(), $nodeTypeName);
		self::$nodesTypes[$nodeTypeId] = $nodeType;

		self::softCleanNodeTypeCache();
		return true;
	}

	/**
	 * Get node type by object
	 *
	 * Does the same as getNodeByObject but with the node type
	 *
	 * @param object $object
	 * @return null|EncoderNode Returns
	 *
	 * @see EncoderNode::getNodeByObject()
	 */
	public static function getNodeTypeByObject($object) {
		$cache = self::$getNodeTypeByObjectCache;
		$className = get_class($object);
		if (isset($cache[$className])) {
			return $cache[$className];
		}
		$nodeTemp = null;
		foreach (self::getNodeTypes() as $node) {
			if ($node->nodeIsObject($object)) {
				$nodeTemp = $node;
			}
		}
		self::$getNodeTypeByObjectCache[$className] = $nodeTemp;
		return $nodeTemp;
	}

	/**
	 * @param string $nodeName Leave empty to return all available node types. Provide a node name to return all node
	 * types based on the same node
	 * @return EncoderNode[] Returns all node type objects fitting the query
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

	/**
	 * Get a single node type
	 *
	 * @param string $nodeName
	 * @param string $nodeType
	 * @return null|EncoderNode Returns the found node type. Otherwise returns null
	 */
	public static function getNodeType($nodeName, $nodeType = EncoderNode::DEFAULT_TYPE) {
		$nodeTypeId = self::getNodeTypeId($nodeName, $nodeType);
		if (isset(self::$nodesTypes[$nodeTypeId])) {
			return self::$nodesTypes[$nodeTypeId];
		}
		return null;
	}

	/**
	 * @param string $nodeName
	 * @param string $nodeType
	 * @return bool Return true if the node type exists
	 */
	public static function nodeTypeExists($nodeName, $nodeType) {
		return self::getNodeType($nodeName, $nodeType) != null;
	}

	/**
	 * Used for generating a full node type id that is used for storing node type
	 *
	 * @param string $nodeName
	 * @param string $nodeType
	 * @return string Returns the id for a certain node type
	 */
	protected static function getNodeTypeId($nodeName, $nodeType) {
		return strtolower($nodeName) . ':' . lcfirst($nodeType);
	}

	/**
	 * Cleans the node cache by filtering out all null values
	 */
	protected static function softCleanNodeCache() {
		self::$getNodeByObjectCache = array_filter(self::$getNodeByObjectCache, 'strlen');
	}
	/**
	 * Cleans the node type cache by filtering out all null values
	 */
	protected static function softCleanNodeTypeCache() {
		self::$getNodeTypeByObjectCache = array_filter(self::$getNodeTypeByObjectCache, 'strlen');
	}

	/**
	 * Resets all registered node and node types
	 */
	public static function clean() {
		self::$nodes = array();
		self::$nodesTypes = array();
		self::$getNodeByObjectCache = array();
		self::$getNodeTypeByObjectCache = array();
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




	public function applyToVariable($name, $parameters) {
		$variable = $this->getVariable($name);
		if ($variable == null) {
			return false;
		}
		return $variable->applyToSetter($this, $parameters);
	}

	public function addVariable(EncoderNodeVariable $variable) {
		return $this->variables->addNodeVariable($variable);
	}

	/**
	 * @param $variable
	 * @param $options
	 * @return bool
	 */
	public function alterVariable($variable, $options) {
		return $this->variables->alterVariable($variable, $options);
	}

	/**
	 * @param $variable
	 * @return EncoderNodeVariable
	 */
	public function getVariable($variable) {
		return $this->variables->getVariable($variable);
	}

	/**
	 * @param $id
	 * @return null|EncoderNodeVariable
	 */
	public function getVariableById($id) {
		return $this->variables->getVariableById($id);
	}

	/**
	 * @param $type
	 * @return EncoderNodeVariable[]
	 */
	public function getVariablesSetterActionByType($type) {
		return $this->variables->getVariablesSetterActionByType($type);
	}

	/**
	 * @param $type
	 * @return EncoderNodeVariable[]
	 */
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