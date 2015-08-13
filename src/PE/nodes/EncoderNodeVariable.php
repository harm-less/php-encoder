<?php

namespace PE\Nodes;

use PE\Exceptions\EncoderNodeVariableException;
use PE\Enums\ActionVariable;
use PE\Library\Inflector;
use PE\Variables\Variable;

class EncoderNodeVariable extends Variable {

	private $setterAction;
	private $getterAction;

	private $_cache;

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

	public function mustBeUnique($bool = null) {
		if ($bool !== null && is_bool($bool)) {
			$this->mustBeUnique = $bool;
		}
		return (bool) $this->mustBeUnique;
	}

	public function alwaysExecute($bool = null) {
		if ($bool !== null && is_bool($bool)) {
			$this->alwaysExecute = $bool;
		}
		return (bool) $this->alwaysExecute;
	}

	public function setSetterAction($method) {
		$this->_cacheReset('getSetterActionMethod');
		$this->_cacheReset('getSetterActionType');
		$this->setterAction = $method;
	}
	public function getSetterAction() {
		return $this->setterAction;
	}
	public function hasSetterAction() {
		return $this->getSetterAction() != null;
	}
	public function getSetterActionMethod() {
		if (($result = self::_cache(__FUNCTION__)) === false) {
			$result = $this->getActionMethod($this->getSetterAction());
			self::_cache(__FUNCTION__, $result);
		}
		return $result;
	}
	public function getSetterActionType() {
		if (($result = self::_cache(__FUNCTION__)) === false) {
			$result = $this->getActionType($this->getSetterAction());
			self::_cache(__FUNCTION__, $result);
		}
		return $result;
	}

	public function setGetterAction($method) {
		$this->_cacheReset('getGetterActionMethod');
		$this->_cacheReset('getGetterActionType');
		$this->getterAction = $method;
	}
	public function getGetterAction() {
		return $this->getterAction;
	}
	public function hasGetterAction() {
		return $this->getGetterAction() != null;
	}
	public function getGetterActionMethod() {
		if (($result = self::_cache(__FUNCTION__)) === false) {
			$result = $this->getActionMethod($this->getGetterAction());
			self::_cache(__FUNCTION__, $result);
		}
		return $result;
	}
	public function getGetterActionType() {
		if (($result = self::_cache(__FUNCTION__)) === false) {
			$result = $this->getActionType($this->getGetterAction());
			self::_cache(__FUNCTION__, $result);
		}
		return $result;
	}

	public function getActionMethod($action) {
		if (is_array($action) && isset($action['method'])) {
			return $action['method'];
		}
		else if (is_string($action)) {
			return $action;
		}
		return null;
	}
	public function getActionType($action) {
		$type = null;
		if ($action) {
			$type = self::ACTION_TYPE_OBJECT;
			if (is_array($action) && isset($action['type'])) {
				$type = $action['type'];
			}
		}
		return $type;
	}

	public function getName() {
		return $this->getId();
	}

	public function getSetterMethod() {
		if (($result = self::_cache(__FUNCTION__)) === false) {
			$result = parent::getSetterMethod();
			if ($result === null) {
				$result = 'set' . $this->camelCased($this->getName());
			}
			self::_cache(__FUNCTION__, $result);
		}
		return $result;
	}
	public function getGetterMethod() {
		if (($result = self::_cache(__FUNCTION__)) === false) {
			$result = parent::getGetterMethod();
			if ($result === null) {
				$result = 'get' . $this->camelCased($this->getName());
			}
			self::_cache(__FUNCTION__, $result);
		}
		return $result;
	}

	public function camelCased($str) {
		return ucfirst(Inflector::camelize($str, true, '-'));
	}

	public function callNodeSetterAction($node, $nodeData, $options) {
		if ($this->getSetterActionType() != self::ACTION_TYPE_NODE) {
			return null;
		}
		$required = array(
			self::ACTION_VARIABLE_SETTER_NODE_DATA
		);
		if (!isset($options[self::ACTION_VARIABLE_SETTER_NODE_DATA])) {
			$options[self::ACTION_VARIABLE_SETTER_NODE_DATA] = $nodeData;
		}
		$variables = $this->_setupNodeActionVariables($this->getSetterAction(), $required, $options);
		return $this->_callNodeAction($node, $this->getSetterActionMethod(), $nodeData, $variables);
	}
	public function callNodeGetterAction($node, $nodeData, $options) {
		if ($this->getGetterActionType() != self::ACTION_TYPE_NODE) {
			return null;
		}
		$required = array(
			self::ACTION_VARIABLE_GETTER_NODE_DATA
		);
		if (!isset($options[self::ACTION_VARIABLE_GETTER_NODE_DATA])) {
			$options[self::ACTION_VARIABLE_GETTER_NODE_DATA] = $nodeData;
		}
		$variables = $this->_setupNodeActionVariables($this->getGetterAction(), $required, $options);
		return $this->_callNodeAction($node, $this->getGetterActionMethod(), $nodeData, $variables);
	}

	protected function _setupNodeActionVariables($action, $required, $options) {

		$variablesNames = array_merge($required, isset($action['variables']) ? $action['variables'] : array());

		$variables = array();
		foreach ($variablesNames as $actionVariableIndex => $variableName) {
			if (array_key_exists($variableName, $options)) {
				$variables[] = $options[$variableName];
			}
			else {
				throw new EncoderNodeVariableException(sprintf('Action variable "%s" is not known', $variableName));
			}
		}
		return $variables;
	}

	protected function _callNodeAction($node, $actionMethodName, $nodeData, $actionVariables) {
		if ($actionMethodName) {
			return call_user_func_array(array($node, $actionMethodName), $actionVariables);
		}
		return $nodeData;
	}


	public function setToObject($node, $nodeData, $parent, $object, $value) {

        $variableAction = $this->getSetterAction();

        $methodResult = null;
        $methodName = $this->getSetterMethod();
		if ($methodName !== false) {
			if ($methodName === null) {
				throw new EncoderNodeVariableException(sprintf('A setter method (%s) for class %s has not been set. Please set it with "setSetterMethod" or with the "setter" key.', $this->getName(), get_class($object)));
			}
			if (method_exists($object, $methodName)) {
				$methodResult = $object->$methodName($this->processValue($value));
			}
			else {
				//throw new ProxyNodeVariableException(sprintf('Method "%s" does not exist for class %s does not exist', $methodName, get_class($object)));
			}
		}

		$actionType = $this->getSetterActionType();
		if ($actionType != null && $actionType != self::ACTION_TYPE_OBJECT) {
			return $methodResult;
		}

		// call a custom action if one is set
		if ($variableAction) {
			$actionVariables = array($object, $value);

			if (!is_array($variableAction)) {
				$actionMethod = $variableAction;
			}
			else {
				$actionMethod = $variableAction['method'];

				if (isset($variableAction['variables'])) {
					$actionVariables = array($object);

					foreach ($variableAction['variables'] as $actionVariable => $actionVariableId) {
						switch ($actionVariableId) {
							case ActionVariable::SETTER_PARENT :
								$actionVariables[] = $parent;
								break;
							case ActionVariable::SETTER_NAME :
								$actionVariables[] = $this->getName();
								break;
							case ActionVariable::SETTER_VALUE :
								$actionVariables[] = $value;
								break;
							case ActionVariable::SETTER_NODE_DATA :
								$actionVariables[] = $nodeData;
								break;
							default :
								throw new EncoderNodeVariableException(sprintf('Action variable id "%s" is not known', $actionVariableId));
								break;
						}
					}
				}
			}
			return call_user_func_array(array($node, $actionMethod), $actionVariables);
		}
		return false;
	}
}