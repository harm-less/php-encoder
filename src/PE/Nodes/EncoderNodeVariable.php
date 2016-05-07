<?php

namespace PE\Nodes;

use PE\Variables\Types\ObjectAccessor;
use PE\Variables\Types\ObjectGetter;
use PE\Variables\Types\ObjectSetter;
use PE\Variables\Types\PostNodeGetter;
use PE\Variables\Types\PostNodeSetter;
use PE\Variables\Types\PreNodeGetter;
use PE\Variables\Types\PreNodeSetter;

class EncoderNodeVariable {

	/**
	 * @var string
	 */
	private $id;
	/**
	 * @var int
	 */
	private $order;
	/**
	 * @var string
	 */
	private $type;

	/**
	 * @var PreNodeSetter
	 */
	private $preNodeSetter;

	/**
	 * @var PreNodeGetter
	 */
	private $preNodeGetter;

	/**
	 * @var PostNodeSetter
	 */
	private $postNodeSetter;

	/**
	 * @var PostNodeGetter
	 */
	private $postNodeGetter;

	/**
	 * @var ObjectSetter
	 */
	private $objectSetter;

	/**
	 * @var ObjectGetter
	 */
	private $objectGetter;


	/**
	 * Boolean variable type
	 */
	const TYPE_BOOL = 'bool';
	/**
	 * Array variable type
	 */
	const TYPE_ARRAY = 'array';
	/**
	 * String variable type
	 */
	const TYPE_STRING = 'string';

	function __construct($id, $enableObjectAccessors = true) {
		$this->setId($id);

		if ($enableObjectAccessors) {
			$this->objectSetter(new ObjectSetter());
			$this->objectGetter(new ObjectGetter());
		}
	}

	/**
	 * @param string $id
	 * @return null|string
	 */
	protected function setId($id) {
		$this->id = $id;
		return $id;
	}

	/**
	 * @return string
	 */
	public function getId() {
		return $this->id;
	}


	/**
	 * Sets the data type of the variable
	 * @param string $type
	 */
	public function setType($type) {
		$this->type = $type;
	}
	/**
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}


	/**
	 * Set the order the variable (when it is a collection)
	 *
	 * A lower number means it will end up near the beginning of the array
	 * @param int $index
	 */
	public function setOrder($index) {
		$this->order = $index;
	}
	/**
	 * @return int
	 */
	public function getOrder() {
		return $this->order;
	}


	/**
	 * @param PreNodeSetter $setter
	 * @return PreNodeSetter
	 */
	public function preNodeSetter(PreNodeSetter $setter) {
		$setter->setVariable($this);
		$this->preNodeSetter = $setter;
		return $setter;
	}

	/**
	 * @return bool
	 */
	public function hasPreNodeSetter() {
		return $this->preNodeSetter !== null;
	}

	/**
	 * @return PreNodeSetter
	 */
	public function getPreNodeSetter() {
		return $this->preNodeSetter;
	}


	/**
	 * @param PreNodeGetter $getter
	 * @return PreNodeGetter
	 */
	public function preNodeGetter(PreNodeGetter $getter) {
		$getter->setVariable($this);
		$this->preNodeGetter = $getter;
		return $getter;
	}

	/**
	 * @return bool
	 */
	public function hasPreNodeGetter() {
		return $this->preNodeGetter !== null;
	}

	/**
	 * @return PreNodeGetter
	 */
	public function getPreNodeGetter() {
		return $this->preNodeGetter;
	}


	/**
	 * @param PostNodeSetter $setter
	 * @return PostNodeSetter
	 */
	public function postNodeSetter(PostNodeSetter $setter) {
		$setter->setVariable($this);
		$this->postNodeSetter = $setter;
		return $setter;
	}

	/**
	 * @return bool
	 */
	public function hasPostNodeSetter() {
		return $this->postNodeSetter !== null;
	}

	/**
	 * @return PostNodeSetter
	 */
	public function getPostNodeSetter() {
		return $this->postNodeSetter;
	}


	/**
	 * @param PostNodeGetter $getter
	 * @return PostNodeGetter
	 */
	public function postNodeGetter(PostNodeGetter $getter) {
		$getter->setVariable($this);
		$this->postNodeGetter = $getter;
		return $getter;
	}

	/**
	 * @return bool
	 */
	public function hasPostNodeGetter() {
		return $this->postNodeGetter !== null;
	}

	/**
	 * @return PostNodeGetter
	 */
	public function getPostNodeGetter() {
		return $this->postNodeGetter;
	}



	/**
	 * @param ObjectAccessor $setter
	 * @return ObjectAccessor
	 */
	public function objectSetter(ObjectAccessor $setter) {
		$setter->setVariable($this);
		$this->objectSetter = $setter;
		return $setter;
	}

	/**
	 * @return bool
	 */
	public function hasObjectSetter() {
		return $this->objectSetter !== null;
	}

	/**
	 * @return ObjectSetter
	 */
	public function getObjectSetter() {
		return $this->objectSetter;
	}



	/**
	 * @param ObjectAccessor $getter
	 * @return ObjectAccessor
	 */
	public function objectGetter(ObjectAccessor $getter) {
		$getter->setVariable($this);
		$this->objectGetter = $getter;
		return $getter;
	}

	/**
	 * @return bool
	 */
	public function hasObjectGetter() {
		return $this->objectGetter !== null;
	}

	/**
	 * @return ObjectGetter
	 */
	public function getObjectGetter() {
		return $this->objectGetter;
	}



	/**
	 * Call the setter action of the node. The setter action type must be set to "node", otherwise it will not execute.
	 *
	 * @param EncoderNode $node The EncoderNode of which you wish to call the setter action from
	 * @param array $options All the available options
	 * @return mixed|null Returns null if setter action type is not "node". Returns an array with the possibly altered node data
	 */
	/*public function callNodeSetterAction(EncoderNode $node, $options) {
		if ($this->getSetterActionType() != self::ACTION_TYPE_NODE) {
			return null;
		}
		$required = array(
			self::ACTION_VARIABLE_SETTER_NODE_DATA
		);
		$variables = $this->_setupNodeActionVariables($this->getSetterAction(), $required, $options);
		return $this->_callNodeAction($node, $this->getSetterActionMethod(), $variables);
	}*/

	/**
	 * Call the getter action of the node. The getter action type must be set to "node", otherwise it will not execute.
	 *
	 * @param EncoderNode $node The EncoderNode of which you wish to call the getter action from
	 * @param array $options All the available options
	 * @return mixed|null Returns null if getter action type is not "node". Returns an array with the possibly altered node data
	 */
	/*public function callNodeGetterAction(EncoderNode $node, $options) {
		if ($this->getGetterActionType() != self::ACTION_TYPE_NODE) {
			return null;
		}
		$required = array(
			self::ACTION_VARIABLE_GETTER_NODE_DATA
		);
		$variables = $this->_setupNodeActionVariables($this->getGetterAction(), $required, $options);
		return $this->_callNodeAction($node, $this->getGetterActionMethod(), $variables);
	}*/

	/**
	 * Prepares the variables so they can be used
	 *
	 * @param array $action Action object
	 * @param array $required Will be used to determine if options are missing or not
	 * @param array $options All available options. If options are missing an error will be thrown
	 * @return array The prepared array of variables
	 */
	/*protected function _setupNodeActionVariables($action, $required, $options) {

		$variablesNames = array_merge($required, isset($action['variables']) ? $action['variables'] : array());

		$variables = array();
		foreach ($variablesNames as $variableName) {
			if (array_key_exists($variableName, $options)) {
				$variables[] = $options[$variableName];
			}
			else {
				throw new EncoderNodeVariableException(sprintf('Action variable "%s" is not known', $variableName));
			}
		}
		return $variables;
	}*/
}