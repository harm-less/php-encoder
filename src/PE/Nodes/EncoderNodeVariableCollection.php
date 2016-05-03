<?php

namespace PE\Nodes;

use PE\Exceptions\EncoderNodeVariableCollectionException;
use PE\Exceptions\EncoderNodeVariableException;
use PE\Variables\Types\NodeAccessor;
use PE\Variables\Types\ObjectAccessor;

class EncoderNodeVariableCollection {

	private $_cache;

	/**
	 * @var EncoderNodeVariable[]
	 */
	private $variables;

	function __construct() {
		$this->variables = array();
	}

	/**
	 * Adds a EncoderNodeVariable to the collection
	 * @param EncoderNodeVariable $variable
	 * @return EncoderNodeVariable
	 */
	public function addVariable(EncoderNodeVariable $variable) {
		$id = $variable->getId();
		if (!$this->variableExists($id)) {
			$this->variables[$id] = $variable;
		}
		$this->_cacheHardReset();
		return $variable;
	}

	/**
	 * @param EncoderNodeVariable|string $variable
	 * @return EncoderNodeVariable
	 */
	public function getVariable($variable) {
		if (is_string($variable)) {
			if ($object = $this->getVariableById($variable)) {
				return $object;
			}
		}
		else if (is_object($variable)) {
			return $variable;
		}
		return null;
	}

	/**
	 * Get a variable based on its id
	 * @param $id
	 * @return null|EncoderNodeVariable
	 */
	public function getVariableById($id) {
		return $this->variableExists($id) ? $this->variables[$id] : null;
	}

	/**
	 * @param string $id
	 * @return bool
	 */
	public function variableExists($id) {
		return isset($this->variables[$id]);
	}


	/**
	 * @param bool $ordered
	 * @return EncoderNodeVariable[]
	 */
	public function getPreNodeSetterVariables($ordered = true) {
		$parameters = $ordered;
		if (($result = $this->_cache(__FUNCTION__, $parameters)) === false) {
			$result = $this->_getVariables('has' . ucfirst(NodeAccessor::ORDER_PRE) . 'Node' . ucfirst(NodeAccessor::ACCESSOR_SETTER), $ordered);
			$this->_cache(__FUNCTION__, $parameters, $result);
		}
		return $result;
	}

	/**
	 * @param bool $ordered
	 * @return EncoderNodeVariable[]
	 */
	public function getPreNodeGetterVariables($ordered = true) {
		$parameters = $ordered;
		if (($result = $this->_cache(__FUNCTION__, $parameters)) === false) {
			$result = $this->_getVariables('has' . ucfirst(NodeAccessor::ORDER_PRE) . 'Node' . ucfirst(NodeAccessor::ACCESSOR_GETTER), $ordered);
			$this->_cache(__FUNCTION__, $parameters, $result);
		}
		return $result;
	}


	/**
	 * @param bool $ordered
	 * @return EncoderNodeVariable[]
	 */
	public function getPostNodeSetterVariables($ordered = true) {
		$parameters = $ordered;
		if (($result = $this->_cache(__FUNCTION__, $parameters)) === false) {
			$result = $this->_getVariables('has' . ucfirst(NodeAccessor::ORDER_POST) . 'Node' . ucfirst(NodeAccessor::ACCESSOR_SETTER), $ordered);
			$this->_cache(__FUNCTION__, $parameters, $result);
		}
		return $result;
	}

	/**
	 * @param bool $ordered
	 * @return EncoderNodeVariable[]
	 */
	public function getPostNodeGetterVariables($ordered = true) {
		$parameters = $ordered;
		if (($result = $this->_cache(__FUNCTION__, $parameters)) === false) {
			$result = $this->_getVariables('has' . ucfirst(NodeAccessor::ORDER_POST) . 'Node' . ucfirst(NodeAccessor::ACCESSOR_GETTER), $ordered);
			$this->_cache(__FUNCTION__, $parameters, $result);
		}
		return $result;
	}


	/**
	 * @param bool $ordered
	 * @return EncoderNodeVariable[]
	 */
	public function getObjectSetterVariables($ordered = true) {
		$parameters = $ordered;
		if (($result = $this->_cache(__FUNCTION__, $parameters)) === false) {
			$result = $this->_getVariables('hasObject' . ucfirst(ObjectAccessor::ACCESSOR_SETTER), $ordered);
			$this->_cache(__FUNCTION__, $parameters, $result);
		}
		return $result;
	}

	/**
	 * @param bool $ordered
	 * @return EncoderNodeVariable[]
	 */
	public function getObjectGetterVariables($ordered = true) {
		$parameters = $ordered;
		if (($result = $this->_cache(__FUNCTION__, $parameters)) === false) {
			$result = $this->_getVariables('hasObject' . ucfirst(ObjectAccessor::ACCESSOR_GETTER), $ordered);
			$this->_cache(__FUNCTION__, $parameters, $result);
		}
		return $result;
	}

	/**
	 * @param string $methodNameHas
	 * @param bool $ordered
	 * @return EncoderNodeVariable[]
	 */
	protected function _getVariables($methodNameHas, $ordered = true) {
		$orderedVariables = array();
		$unorderedVariables = array();
		foreach ($this->variables as $variable) {
			if (!$variable->{$methodNameHas}()) {
				// the variable does not have an object variable so continue the loop
				continue;
			}
			$orderPosition = $ordered ? $variable->getOrder() : null;
			if ($orderPosition !== null) {
				if (array_key_exists($orderPosition, $orderedVariables)) {
					throw new EncoderNodeVariableCollectionException(sprintf('Cannot order variables because position "%s" is being used more than once', $orderPosition));
				}
				$orderedVariables[$orderPosition] = $variable;
			}
			else {
				array_push($unorderedVariables, $variable);
			}
		}
		return $ordered ? array_merge($orderedVariables, $unorderedVariables) : $unorderedVariables;
	}


	/**
	 * @param string $method The "has-variable" method name
	 * @param null $parameters
	 * @param bool $value
	 * @return bool
	 */
	protected function _cache($method, $parameters = null, $value = false) {
		$parameters = is_null($parameters) ? '__default__' : (string) $parameters;
		if ($value !== false) {
			if (!isset($this->_cache[$method])) {
				$this->_cache[$method] = array();
			}
			$this->_cache[$method][$parameters] = $value;
			return $value;
		}
		if (!array_key_exists($method, $this->_cache) || !array_key_exists($parameters, $this->_cache[$method])) {
			return false;
		}
		return $this->_cache[$method][$parameters];
	}

	/**
	 * @param string $method
	 * @return bool
	 */
	protected function _cacheReset($method) {
		if (array_key_exists($method, $this->_cache)) {
			unset($this->_cache[$method]);
			return true;
		}
		return false;
	}

	protected function _cacheHardReset() {
		$this->_cache = array();
	}


	/**
	 * @param $dataArray
	 * @param bool $throwErrorIfFails Set to true if you want it to throw an error if it fails
	 * @return bool Returns true if all requirements are met
	 */
	public function objectVariablesAreValidWithData($dataArray, $throwErrorIfFails = false) {
		$variables = $this->getObjectSetterVariables(false);
		$unique = array();
		foreach ($dataArray as $data) {
			foreach ($variables as $variable) {
				$variableId = $variable->getId();
				if ($variableId !== null && array_key_exists($variableId, $data)) {
					if ($variable->getObjectSetter()->mustBeUnique()) {
						if (!isset($unique[$variableId])) {
							$unique[$variableId] = array();
						}
						$variableValue = $data[$variableId];
						if (array_search($variableValue, $unique[$variableId]) !== false) {
							if ($throwErrorIfFails) {
								throw new EncoderNodeVariableException(sprintf('Variable "%s" must be unique but value "%s" is given at least twice', $variableId, $variableValue));
							}
							else {
								return false;
							}
						}
						$unique[$variableId][] = $variableValue;
					}
				}
			}
		}
		return true;
	}
} 