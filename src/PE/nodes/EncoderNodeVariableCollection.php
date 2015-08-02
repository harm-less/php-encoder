<?php

namespace PE\Nodes;

use PE\Exceptions\EncoderNodeVariableException;
use PE\Variables\Variable;
use PE\Variables\VariableCollection;

class EncoderNodeVariableCollection extends VariableCollection {

	private $_cachedAlwaysExecutedVariables;

	public function addNodeVariable(EncoderNodeVariable $variable) {
		$this->_cachedAlwaysExecutedVariables = null;
		$variable = parent::addVariable($variable);
		return $variable;
	}
	public function addVariable(Variable $variable) {
		throw new EncoderNodeVariableException('Use "addNodeVariable" to add variables');
	}

	public function variablesAreValidWithData($dataArray) {
		$unique = array();
		foreach ($dataArray as $data) {
			foreach ($this->getVariables() as $variable) {
				$variableId = $variable->getId();
				if ($variableId !== null) {
					if (array_key_exists($variableId, $data)) {
						$variableValue = $data[$variableId];
						if ($variable->mustBeUnique()) {
							if (!isset($unique[$variableId])) {
								$unique[$variableId] = array();
							}
							if (array_search($variableValue, $unique[$variableId]) !== false) {
								throw new EncoderNodeVariableException(sprintf('Variable "%s" must be unique but value "%s" is given at least twice', $variableId, $variableValue));
							}
							$unique[$variableId][] = $variableValue;
						}
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
} 