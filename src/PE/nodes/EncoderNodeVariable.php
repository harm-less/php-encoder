<?php

namespace PE\Nodes;

use PE\Exceptions\EncoderNodeVariableException;
use PE\Enums\ActionVariable;
use PE\Library\Inflector;
use PE\Variables\Variable;

class EncoderNodeVariable extends Variable {

	private $_cache;

	private $setterAction;
	private $getterAction;

	private $alwaysExecute;
	private $mustBeUnique;

	const ACTION_TYPE_NODE = 'node';
	const ACTION_TYPE_OBJECT = 'object';

	const ACTION_VARIABLE_SETTER_NODE_DATA = 'node_data';
	const ACTION_VARIABLE_GETTER_NODE_DATA = 'node_data';

	function __construct($id, $options = null) {
		$this->_cache = array();

		parent::__construct($options, $id);
	}

	public function parseOptions($options) {
		$options = (array) $options;
		foreach ($options as $option => $value) {
			switch ($option) {
				case 'setterAction' :
					$this->setSetterAction($value);
					break;
				case 'getterAction' :
					$this->setGetterAction($value);
					break;
				case 'unique' :
					$this->mustBeUnique($value);
					break;
				case 'alwaysExecute' :
					$this->alwaysExecute($value);
					break;
			}
		}
		parent::parseOptions($options);
	}

	protected function _cache($method, $value = false) {
		if ($value !== false) {
			$this->_cache[$method] = $value;
			return $value;
		}
		if (!array_key_exists($method, $this->_cache)) {
			return false;
		}
		return $this->_cache[$method];
	}
	protected function _cacheReset($method) {
		if (array_key_exists($method, $this->_cache)) {
			unset($this->_cache[$method]);
			return true;
		}
		return false;
	}

	/**
	 * This node variable value must unique in relation to other nodes in a series
	 *
	 * @param null|bool $bool Set to true to enable the this variable to be unique. Leave empty to retrieve the current value
	 * @return bool Default is false
	 */
	public function mustBeUnique($bool = null) {
		if ($bool !== null && is_bool($bool)) {
			$this->mustBeUnique = $bool;
		}
		return (bool) $this->mustBeUnique;
	}

	/**
	 * This node variable is always executed even though there is not value set. This way you can force a value
	 * to be set manually
	 *
	 * @param null|bool $bool Set to true to enable the this variable to always be executed. Leave empty to retrieve the current value
	 * @return bool Default is false
	 */
	public function alwaysExecute($bool = null) {
		if ($bool !== null && is_bool($bool)) {
			$this->alwaysExecute = $bool;
		}
		return (bool) $this->alwaysExecute;
	}

	/**
	 * Set the setter action method. The way the variable knows how to set the value when it's his turn
	 *
	 * ```php
	 * $variable = new EncoderNodeVariable('variableName');
	 * // string
	 * $variable->setSetterAction('methodName');
	 *
	 * // array - available options: method (required), type, variables
	 * $variable->setSetterAction(array(
	 *   'method' => 'methodName',
	 *   'type' => EncoderNodeVariable::ACTION_TYPE_NODE,
	 *   'variable' => array(ActionVariable::SETTER_VALUE, ActionVariable::SETTER_PARENT);
	 * ));
	 * ```
	 * @param string|array $method Can either be the name of the method in the object as a string or an array like
	 * in the example
	 */
	public function setSetterAction($method) {
		if (!(is_string($method) || (is_array($method) && isset($method['method']) && is_string($method['method'])))) {
			throw new EncoderNodeVariableException('Either method must be a string or an array with a "method" key being a string');
		}
		$this->_cacheReset('getSetterActionMethod');
		$this->_cacheReset('getSetterActionType');
		$this->setterAction = $method;
	}

	/**
	 * @return string|array Get the current setter action
	 */
	public function getSetterAction() {
		return $this->setterAction;
	}

	/**
	 * @return bool Does it have a setter action or not?
	 */
	public function hasSetterAction() {
		return $this->getSetterAction() != null;
	}

	/**
	 * Specifically get the method of the setter action
	 * @return false|string Either returns false if no setter action method has been set or returns the string of the method
	 */
	public function getSetterActionMethod() {
		if (($result = self::_cache(__FUNCTION__)) === false) {
			// it's being cached because it is requested a lot
			$result = $this->getActionMethod($this->getSetterAction());
			self::_cache(__FUNCTION__, $result);
		}
		return $result;
	}

	/**
	 * Specifically get the type of the setter action
	 * @return false|string Either returns false if no setter action type has been set or returns the type of method
	 */
	public function getSetterActionType() {
		if (($result = self::_cache(__FUNCTION__)) === false) {
			// it's being cached because it is requested a lot
			$result = $this->getActionType($this->getSetterAction());
			self::_cache(__FUNCTION__, $result);
		}
		return $result;
	}

	/**
	 * Set the getter action method. The way the variable knows how to get the value for this variable
	 *
	 * @param string|array $method Can either be the name of the method in the object as a string or an array like
	 * @see EncoderNodeVariable::setSetterAction() It shows what to provide to this method
	 */
	public function setGetterAction($method) {
		if (!(is_string($method) || (is_array($method) && isset($method['method']) && is_string($method['method'])))) {
			throw new EncoderNodeVariableException('Either method must be a string or an array with a "method" key being a string');
		}
		$this->_cacheReset('getGetterActionMethod');
		$this->_cacheReset('getGetterActionType');
		$this->getterAction = $method;
	}

	/**
	 * @return string|array Get the current getter action
	 */
	public function getGetterAction() {
		return $this->getterAction;
	}

	/**
	 * @return bool Does it have a getter action or not?
	 */
	public function hasGetterAction() {
		return $this->getGetterAction() != null;
	}

	/**
	 * Specifically get the method of the getter action
	 * @return false|string Either returns false if no getter action method has been set or returns the string of the method
	 */
	public function getGetterActionMethod() {
		if (($result = self::_cache(__FUNCTION__)) === false) {
			$result = $this->getActionMethod($this->getGetterAction());
			self::_cache(__FUNCTION__, $result);
		}
		return $result;
	}

	/**
	 * Specifically get the type of the getter action
	 * @return false|string Either returns false if no getter action type has been set or returns the type of method
	 */
	public function getGetterActionType() {
		if (($result = self::_cache(__FUNCTION__)) === false) {
			$result = $this->getActionType($this->getGetterAction());
			self::_cache(__FUNCTION__, $result);
		}
		return $result;
	}

	/**
	 * Generic method to get the action method
	 *
	 * @param string|array $action Action object or string
	 * @return null|string Returns null if nothing has been found or the string if it has
	 */
	protected function getActionMethod($action) {
		if (is_array($action) && isset($action['method'])) {
			return $action['method'];
		}
		else if (is_string($action)) {
			return $action;
		}
		return null;
	}

	/**
	 * Generic method to get the action type
	 *
	 * @param string|array $action Action object or string
	 * @return null|string Returns null if nothing has been found or the type if it has. If no type has been set but
	 * there is an action object, the default is "object"
	 */
	protected function getActionType($action) {
		$type = null;
		if ($action) {
			$type = self::ACTION_TYPE_OBJECT;
			if (is_array($action) && isset($action['type'])) {
				$type = $action['type'];
			}
		}
		return $type;
	}

	/**
	 * Returns the name of the variable
	 *
	 * @return string The id supplied to this instance is the name
	 */
	public function getName() {
		return $this->getId();
	}

	/**
	 * @return false|string Returns the setter method if available. Otherwise it returns false
	 *
	 * @todo Figure out if this is still necessary because the actions have this feature too
	 */
	public function getSetterMethod() {
		if (($result = self::_cache(__FUNCTION__)) === false) {
			$result = parent::getSetterMethod();
			if ($result === null && $this->getName()) {
				$result = 'set' . $this->camelCased($this->getName());
			}
			self::_cache(__FUNCTION__, $result);
		}
		return $result;
	}

	/**
	 * @return false|string Returns the getter method if available. Otherwise it returns false
	 *
	 * @todo Figure out if this is still necessary because the actions have this feature too
	 */
	public function getGetterMethod() {
		if (($result = self::_cache(__FUNCTION__)) === false) {
			$result = parent::getGetterMethod();
			if ($result === null && $this->getName()) {
				$result = 'get' . $this->camelCased($this->getName());
			}
			self::_cache(__FUNCTION__, $result);
		}
		return $result;
	}

	/**
	 * @param string $str Spinal cased string
	 * @return string Camel cased string
	 */
	protected function camelCased($str) {
		return ucfirst(Inflector::camelize($str, true, '-'));
	}

	/**
	 * Call the setter action of the node. The setter action type must be set to "node", otherwise it will not execute.
	 *
	 * @param EncoderNode $node The EncoderNode of which you wish to call the setter action from
	 * @param array $options All the available options
	 * @return mixed|null Returns null if setter action type is not "node". Returns an array with the possibly altered node data
	 */
	public function callNodeSetterAction(EncoderNode $node, $options) {
		if ($this->getSetterActionType() != self::ACTION_TYPE_NODE) {
			return null;
		}
		$required = array(
			self::ACTION_VARIABLE_SETTER_NODE_DATA
		);
		$variables = $this->_setupNodeActionVariables($this->getSetterAction(), $required, $options);
		return $this->_callNodeAction($node, $this->getSetterActionMethod(), $variables);
	}

	/**
	 * Call the getter action of the node. The getter action type must be set to "node", otherwise it will not execute.
	 *
	 * @param EncoderNode $node The EncoderNode of which you wish to call the getter action from
	 * @param array $options All the available options
	 * @return mixed|null Returns null if getter action type is not "node". Returns an array with the possibly altered node data
	 */
	public function callNodeGetterAction(EncoderNode $node, $options) {
		if ($this->getGetterActionType() != self::ACTION_TYPE_NODE) {
			return null;
		}
		$required = array(
			self::ACTION_VARIABLE_GETTER_NODE_DATA
		);
		$variables = $this->_setupNodeActionVariables($this->getGetterAction(), $required, $options);
		return $this->_callNodeAction($node, $this->getGetterActionMethod(), $variables);
	}

	/**
	 * Prepares the variables so they can be used
	 *
	 * @param array $action Action object
	 * @param array $required Will be used to determine if options are missing or not
	 * @param array $options All available options. If options are missing an error will be thrown
	 * @return array The prepared array of variables
	 */
	protected function _setupNodeActionVariables($action, $required, $options) {

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
	}

	/**
	 * Makes a call to the actual node method
	 *
	 * @param EncoderNode $node Node being used to make the call
	 * @param string $actionMethodName The method name from the node being called
	 * @param array $parameters All variables being set to the node in the order they are supplied
	 * @return mixed
	 */
	protected function _callNodeAction(EncoderNode $node, $actionMethodName, $parameters) {
		return call_user_func_array(array($node, $actionMethodName), $parameters);
	}

	/**
	 * Depending on he settings in this variable it will either call a method in the supplied EncoderNode or in the
	 * supplied object. The node will be called if a setterAction has been set with a type of
	 * EncoderNodeVariable::ACTION_TYPE_NODE. Otherwise it will call the object. Depending on the method it chooses,
	 * certain parameter are required.
	 *
	 * @param array $parameters Associated array based on all "SETTER_*" constants from ActionVariable.
	 * @return mixed Returns whatever the object returns
	 *
	 * @see ActionVariable All "SETTER_*" constants can be a key of the $parameters array
	 */
	public function applyToSetter($parameters) {
		if ($this->hasSetterAction() && $this->getSetterActionType() === EncoderNodeVariable::ACTION_TYPE_NODE) {
			return $this->applyToNodeSetter($parameters);
		}
		else {
			return $this->applyToObjectSetter($parameters[ActionVariable::SETTER_OBJECT], $parameters[ActionVariable::SETTER_VALUE]);
		}
	}

	/**
	 * Applies a certain value to certain object
	 *
	 * @param object $object The object you want to have called
	 * @param mixed $value The value this method should receive
	 * @return mixed Returns whatever the object returns
	 */
	protected function applyToObjectSetter($object, $value) {
		$methodName = $this->getSetterActionMethod() ? $this->getSetterActionMethod() : $this->getSetterMethod();
		if (!method_exists($object, $methodName)) {
			throw new EncoderNodeVariableException(sprintf('Method "%s" does not exist for class %s does not exist', $methodName, get_class($object)));
		}
		else {
			return $object->$methodName($this->processValue($value));
		}
	}

	/**
	 * Calls a certain node and provides any variables the method requires
	 *
	 * @param array $parameters Associated array based on all "SETTER_*" constants from ActionVariable.
	 * @return mixed Returns whatever the object returns
	 *
	 * @see ActionVariable All "SETTER_*" constants can be a key of the $parameters array
	 */
	protected function applyToNodeSetter($parameters) {

		$nodeData = $parameters[ActionVariable::SETTER_NODE_DATA];

		$actionMethod = $this->getSetterActionMethod();

		// get custom parameters and prepend the object in front of it
		$customParameters = $this->gatherAccessorParameters($parameters);
		if ($customParameters) {
			array_unshift($customParameters, $nodeData);
		}
		// if it fails to get any kind of custom object, create a default one
		$methodParameters = $customParameters ? $customParameters : array($nodeData, $parameters[ActionVariable::SETTER_VALUE]);

		// using those parameters, call the node action method
		return $this->_callNodeAction($parameters[ActionVariable::SETTER_NODE], $actionMethod, $methodParameters);
	}

	/**
	 * Gathers all required variables
	 *
	 * @param array $parameters Associated array based on all "SETTER_*" constants from ActionVariable.
	 * @return array Returns an array with all required parameters extracted from the $parameters variable in the
	 * right order.
	 *
	 * @see ActionVariable All "SETTER_*" constants can be a key of the $parameters array
	 */
	protected function gatherAccessorParameters($parameters) {
		$actionVariables = array();
		$variableAction = $this->getSetterAction();
		if (isset($variableAction['variables']) && count($variableAction['variables'])) {
			foreach ($variableAction['variables'] as $actionVariableId) {
				if (!isset($parameters[$actionVariableId])) {
					throw new EncoderNodeVariableException(sprintf('Action variable id "%s" is not known', $actionVariableId));
				}
				array_push($actionVariables, $parameters[$actionVariableId]);
			}
		}
		return $actionVariables;
	}
}