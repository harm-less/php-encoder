<?php

namespace PE\Nodes;

use PE\Variables\Types\ObjectAccessor;
use PE\Variables\Types\ObjectGetter;
use PE\Variables\Types\ObjectSetter;
use PE\Variables\Types\PostNodeGetter;
use PE\Variables\Types\PostNodeSetter;
use PE\Variables\Types\PreNodeGetter;
use PE\Variables\Types\PreNodeSetter;
use PE\Variables\Variable;

class EncoderNodeVariable extends Variable {

	private $preNodeSetter;
	private $preNodeGetter;
	private $postNodeSetter;
	private $postNodeGetter;
	private $objectSetter;
	private $objectGetter;

	function __construct($id, $enableObjectAccessors = true) {
		parent::__construct($id);

		if ($enableObjectAccessors) {
			$this->objectSetter(new ObjectSetter());
			$this->objectGetter(new ObjectGetter());
		}
	}

	public function preNodeSetter(PreNodeSetter $setter) {
		$setter->setVariable($this);
		$this->preNodeSetter = $setter;
		return $setter;
	}
	public function hasPreNodeSetter() {
		return $this->preNodeSetter !== null;
	}
	public function getPreNodeSetter() {
		return $this->preNodeSetter;
	}

	public function preNodeGetter(PreNodeGetter $getter) {
		$getter->setVariable($this);
		$this->preNodeGetter = $getter;
		return $getter;
	}
	public function hasPreNodeGetter() {
		return $this->preNodeGetter !== null;
	}
	public function getPreNodeGetter() {
		return $this->preNodeGetter;
	}
	

	public function postNodeSetter(PostNodeSetter $setter) {
		$setter->setVariable($this);
		$this->postNodeSetter = $setter;
		return $setter;
	}
	public function hasPostNodeSetter() {
		return $this->postNodeSetter !== null;
	}
	public function getPostNodeSetter() {
		return $this->postNodeSetter;
	}

	public function postNodeGetter(PostNodeGetter $getter) {
		$getter->setVariable($this);
		$this->postNodeGetter = $getter;
		return $getter;
	}
	public function hasPostNodeGetter() {
		return $this->postNodeGetter !== null;
	}
	public function getPostNodeGetter() {
		return $this->postNodeGetter;
	}

	
	public function objectSetter(ObjectAccessor $setter) {
		$setter->setVariable($this);
		$this->objectSetter = $setter;
		return $setter;
	}
	public function hasObjectSetter() {
		return $this->objectSetter !== null;
	}
	public function getObjectSetter() {
		return $this->objectSetter;
	}
	
	public function objectGetter(ObjectAccessor $getter) {
		$getter->setVariable($this);
		$this->objectGetter = $getter;
		return $getter;
	}
	public function hasObjectGetter() {
		return $this->objectGetter !== null;
	}
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