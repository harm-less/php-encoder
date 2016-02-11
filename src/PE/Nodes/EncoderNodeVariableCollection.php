<?php

namespace PE\Nodes;

use PE\Exceptions\EncoderNodeVariableException;
use PE\Variables\Variable;
use PE\Variables\VariableCollection;

class EncoderNodeVariableCollection extends VariableCollection {

	private $_cachedAlwaysExecutedVariables;

	public function getVariablesSetterActionByType($type) {
		return $this->_getVariablesActionByType($type, 'getSetterAction');
	}
	public function getVariablesGetterActionByType($type) {
		return $this->_getVariablesActionByType($type, 'getGetterAction');
	}

	/**
	 * @param $type
	 * @param $actionMethod
	 * @return EncoderNodeVariable[]
	 */
	protected function _getVariablesActionByType($type, $actionMethod) {
		$variables = array();
		foreach ($this->getVariables() as $variable) {
			$action = $variable->$actionMethod();
			if (isset($action['type']) && $action['type'] == $type) {
				$variables[$variable->getId()] = $variable;
			}
		}
		return $variables;
	}

	/**
	 * @param EncoderNodeVariable $variable
	 * @return EncoderNodeVariable
	 */
	public function addNodeVariable(EncoderNodeVariable $variable) {
		$this->_cachedAlwaysExecutedVariables = null;
		$variable = parent::addVariable($variable);
		return $variable;
	}

	/**
	 * You cannot use this method, use "addNodeVariable" instead
	 *
	 * @param Variable $variable
	 * @return void
	 */
	public function addVariable(Variable $variable) {
		throw new EncoderNodeVariableException('Use "addNodeVariable" to add variables');
	}

	/**
	 * @param $dataArray
	 * @param bool $throwErrorIfFails Set to true if you want it to throw an error if it fails
	 * @return bool Returns true if all requirements are met
	 */
	public function variablesAreValidWithData($dataArray, $throwErrorIfFails = false) {
		$variables = $this->getVariables();
		$unique = array();
		foreach ($dataArray as $data) {
			foreach ($variables as $variable) {
				$variableId = $variable->getId();
				if ($variableId !== null && array_key_exists($variableId, $data)) {
					if ($variable->mustBeUnique()) {
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

	public function getAlwaysExecutedVariables() {
		if ($this->_cachedAlwaysExecutedVariables !== null) {
			return $this->_cachedAlwaysExecutedVariables;
		}
		$variables = array();
		foreach ($this->getVariables() as $variable) {
			if ($variable->alwaysExecute()) {
				$variables[$variable->getId()] = $variable;
			}
		}
		$this->_cachedAlwaysExecutedVariables = $variables;
		return $variables;
	}

	/**
	 * Overridden method so the returned data type corresponds with this class
	 *
	 * @param bool $order
	 * @return EncoderNodeVariable[]
	 */
	public function getVariables($order = true) {
		return parent::getVariables($order);
	}
} 