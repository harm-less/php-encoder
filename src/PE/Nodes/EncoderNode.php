<?php

namespace PE\Nodes;

use PE\Enums\ActionVariable;
use ReflectionClass;
use PE\Exceptions\EncoderNodeException;

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
	/**
	 * @var EncoderNode[]
	 */
	private static $getNodeTypeByObjectCache = array();

	/**
	 * @var EncoderNodeChildren
	 */
	private $children;

	/**
	 * @var EncoderNodeVariableCollection
	 */
	private $variables;

	/**
	 * @var string
	 */
	private $nodeName;
	/**
	 * @var string
	 */
	private $nodeNameSingle;
	/**
	 * @var string
	 */
	private $isolatedNodeName;
	/**
	 * @var string
	 */
	private $typeName;

	/**
	 * @var bool
	 */
	private $needsClass;
	/**
	 * @var string
	 */
	private $classPrepend;

	const DEFAULT_TYPE = 'default';

	const NODE_EXTENSION = 'Node';

	function __construct($nodeName, $nodeNameSingle, $classPrepend, $nodeTypeName = null) {

		$this->nodeClasses = array();
		$this->_nodeIsObjectCache = array();

		$this->children = new EncoderNodeChildren();
		$this->variables = new EncoderNodeVariableCollection();

		$this->setNodeName($nodeName, $nodeNameSingle);

		$this->setNeedsObject(true);
		$this->classPrepend = $classPrepend;
		$this->typeName = $nodeTypeName;
	}

	/**
	 * Adds a node
	 *
	 * This method is essential for using this library because it will instruct it what to do and how to do it.
	 *
	 * @param EncoderNode $node
	 * @return EncoderNode Will return the EncoderNode if the node had been successfully added
	 */
	public static function addNode(EncoderNode $node) {
		$nodeName = $node->getNodeName();
		$nodeNameSingle = $node->getNodeNameSingle();
		if ($nodeName === null || empty($nodeName) || !is_string($nodeName)) {
			throw new EncoderNodeException('Node without a name has been added. It must be a string and it cannot be empty.');
		}
		if ($node->getNodeTypeName() !== null) {
			throw new EncoderNodeException('The node you\'re trying to add seems to be a node type because it has a type name');
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
			// set the default type name so it can be registered as a type
			$node->typeName = self::DEFAULT_TYPE;
			self::addNodeType($node);
		}

		self::softCleanNodeCache();
		return $node;
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
	 * @return EncoderNode Returns the EncoderNode if the node type was successfully added
	 */
	public static function addNodeType(EncoderNode $nodeType) {
		if ($nodeType->getNodeTypeName() === null) {
			throw new EncoderNodeException('The node type you\'re trying to add seems to be a regular node because it has a no type name. Make sure you try to add an EncoderNode with a type name');
		}
		$nodeName = $nodeType->getNodeName();
		$nodeTypeName = $nodeType->getNodeTypeName();
		if (self::nodeTypeExists($nodeName, $nodeTypeName)) {
			throw new EncoderNodeException(sprintf('Node type with name "%s" and node type name "%s" already exists', $nodeName, $nodeTypeName));
		}
		$nodeType->typeName = $nodeTypeName;
		$nodeTypeId = self::getNodeTypeId($nodeType->getNodeName(), $nodeTypeName);
		self::$nodesTypes[$nodeTypeId] = $nodeType;

		self::softCleanNodeTypeCache();
		return $nodeType;
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


	/**
	 * In name-spaced projects it would be a lot of work to supply the full class names all the time.
	 * This variable allows for a shortcut that will prepend the repeated part of a class name.
	 *
	 * @return string Class name it will prepend
	 */
	public function classPrepend() {
		return $this->classPrepend;
	}

	/**
	 * Returns the object name that this node requires
	 *
	 * @return string
	 * @see classPrepend() This will get prepended to this value
	 */
	public function getNodeObjectName() {
		return $this->_getNodeIsolatedClassName();
	}

	/**
	 * @return string Returns the latest value for a namespace
	 */
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

	/**
	 * Check if the supplied object belongs to this node
	 *
	 * @param object $object
	 * @return bool Return true if the object belongs to this node
	 */
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

	/**
	 * @param string $nodeName Node name for array type entries
	 * @param string $nodeNameSingle Node name for single entries
	 */
	protected function setNodeName($nodeName, $nodeNameSingle) {
		$this->nodeName = $nodeName;
		$this->nodeNameSingle = $nodeNameSingle;
	}

	/**
	 * @return string Returns the node name for array type entries
	 */
	public function getNodeName() {
		return $this->nodeName;
	}

	/**
	 * @return string Returns the node name for single entries
	 */
	public function getNodeNameSingle() {
		return $this->nodeNameSingle;
	}

	/**
	 * @return string Returns the node type name
	 */
	public function getNodeTypeName() {
		return $this->typeName;
	}

	/**
	 * @return string Returns the default node type name
	 */
	public function getDefaultType() {
		return EncoderNode::DEFAULT_TYPE;
	}

	/**
	 * @param bool $bool If object is required for a node, set it to true.
	 */
	protected function setNeedsObject($bool) {
		$this->needsClass = $bool;
	}

	/**
	 * @return bool Returns true if an object is required for a node
	 */
	public function needsObject() {
		return $this->needsClass;
	}


	/**
	 * @param EncoderNodeChild $child
	 * @return false|EncoderNodeChild
	 *
	 * @see EncoderNodeChildren::addChild()
	 */
	public function addChildNode(EncoderNodeChild $child) {
		return $this->children->addChild($child);
	}

	/**
	 * @param string $childName
	 * @return null|EncoderNodeChild
	 *
	 * @see EncoderNodeChildren::getChild()
	 */
	public function getChild($childName) {
		return $this->children->getChild($childName);
	}
	/**
	 * @return EncoderNodeChild[]
	 *
	 * @see EncoderNodeChildren::getChildren()
	 */
	public function getChildren() {
		return $this->children->getChildren();
	}

	/**
	 * @param string $childName
	 * @return bool
	 *
	 * @see EncoderNodeChildren::childExists()
	 */
	public function childNodeExists($childName) {
		return $this->children->childExists($childName);
	}

	/**
	 * @param string $childName
	 * @param object $target
	 * @param array $values
	 * @return bool
	 *
	 * @see EncoderNodeChildren::addChildrenToObject()
	 */
	public function addChildrenToObject($childName, $target, $values) {
		return $this->children->addChildrenToObject($childName, $target, $values);
	}




	/**
	 * @param EncoderNodeVariable $variable
	 * @return EncoderNodeVariable
	 *
	 * @see EncoderNodeVariable::addNodeVariable()
	 */
	public function addVariable(EncoderNodeVariable $variable) {
		return $this->variables->addNodeVariable($variable);
	}

	/**
	 * @param $variable
	 * @return EncoderNodeVariable
	 *
	 * @see EncoderNodeVariable::getVariable()
	 */
	public function getVariable($variable) {
		return $this->variables->getVariable($variable);
	}

	/**
	 * @param string $id
	 * @return null|EncoderNodeVariable
	 *
	 * @see EncoderNodeVariable::getVariableById()
	 */
	public function getVariableById($id) {
		return $this->variables->getVariableById($id);
	}

	/**
	 * @param string $type
	 * @return EncoderNodeVariable[]
	 *
	 * @see EncoderNodeVariable::getVariablesSetterActionByType()
	 */
	public function getVariablesSetterActionByType($type) {
		return $this->variables->getVariablesSetterActionByType($type);
	}

	/**
	 * @param string $type
	 * @return EncoderNodeVariable[]
	 *
	 * @see EncoderNodeVariable::getVariablesGetterActionByType()
	 */
	public function getVariablesGetterActionByType($type) {
		return $this->variables->getVariablesGetterActionByType($type);
	}

	/**
	 * @return EncoderNodeVariable[]
	 *
	 * @see EncoderNodeVariable::getAlwaysExecutedVariables()
	 */
	public function getAlwaysExecutedVariables() {
		return $this->variables->getAlwaysExecutedVariables();
	}

	/**
	 * @param bool $order
	 * @return EncoderNodeVariable[]
	 *
	 * @see EncoderNodeVariable::getAlwaysExecutedVariables()
	 */
	public function getVariables($order = true) {
		return $this->variables->getVariables($order);
	}

	/**
	 * @param string $id
	 * @return bool
	 *
	 * @see EncoderNodeVariable::variableExists()
	 */
	public function variableExists($id) {
		return $this->variables->variableExists($id);
	}

	/**
	 * @param array $nodeDataArray
	 * @param bool $throwErrorIfFails Set to true if you want it to throw an error if it fails
	 * @return bool Returns true if all requirements are met
	 *
	 * @see EncoderNodeVariable::variablesAreValidWithData()
	 */
	public function variablesAreValid($nodeDataArray, $throwErrorIfFails = false) {
		return $this->variables->variablesAreValidWithData($nodeDataArray, $throwErrorIfFails);
	}

	/**
	 * @param string $name
	 * @param mixed $value
	 * @return mixed
	 *
	 * @see EncoderNodeVariable::processValue()
	 */
	public function processValue($name, $value) {
		return $this->variables->processValue($name, $value);
	}

	/**
	 * @param string $name Variable name you want to apply the parameters to
	 * @param array $parameters Array of all the information required for the several methods needing it
	 * @return bool|mixed
	 *
	 * @see EncoderNodeVariable::applyToSetter()
	 */
	public function applyToVariable($name, $parameters) {
		$variable = $this->getVariable($name);
		if ($variable == null) {
			return false;
		}
		$parameters[ActionVariable::SETTER_NODE] = $this;
		return $variable->applyToSetter($parameters);
	}






	protected function _loadObject($object) {
		throw new EncoderNodeException('Must be overwritten by subclasses');
	}

	/**
	 * @return string Returns the full class name that will be required for this EncoderNode
	 */
	protected function _objectClassName() {
		return $this->classPrepend() . '\\' . $this->_objectFileName();
	}

	/**
	 * @return string Returns the file name of the class required for this EncoderNode
	 */
	protected function _objectFileName() {
		return $this->_getNodeIsolatedClassName();
	}

	/**
	 * Will load a certain file
	 *
	 * @param string|null $object Name of the object. Leave empty if you want to trigger the "_objectFileName" method
	 * @return mixed
	 *
	 * @see _objectFileName()
	 */
	public function loadObject($object = null) {
		if ($object === null) {
			$object = $this->_objectFileName();
			if ($object === null) {
				throw new EncoderNodeException('Object for loading cannot be null');
			}
		}
		$this->_loadObject($object);
	}

	/**
	 * @return string Returns read-only value of "_objectClassName()"
	 *
	 * @see _objectClassName();
	 */
	public function getObjectClassName() {
		return $this->_objectClassName();
	}




	/**
	 * Figure out which node type is going to be used. It is very handy to extend this method from a node because it
	 * allows for more control over the type next to the default behavior
	 *
	 * @param object $parent A variable with the parent object so you can get some information from there next to the
	 * processed node data
	 * @param array $nodeData Processed node data
	 * @return null|string Returns null is no type is found in the node data. Otherwise returns the type extracted from
	 * the node data
	 */
	public function getObjectType($parent, $nodeData) {
		return isset($nodeData['type']) ? $nodeData['type'] : null;
	}

	/**
	 * Retrieve a node type based on its node type name
	 *
	 * @param string $nodeTypeName
	 * @return EncoderNode|null Returns the EncoderNode object for the requested type. Null if if no type is found
	 */
	public function getType($nodeTypeName) {
		return EncoderNode::getNodeType($this->getNodeName(), $nodeTypeName);
	}
}