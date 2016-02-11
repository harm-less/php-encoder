<?php

namespace PE\Variables;

use PE\Exceptions\VariableCollectionException;

class VariableCollection {

	/**
	 * @var Variable[]
	 */
	private $variables;

	function __construct() {
		$this->variables = array();
	}

	public function processValue($name, $value) {
		$variable = $this->getVariable($name);
		if ($variable === null) {
			return null;
		}
		return $variable->processValue($value);
	}

	public function addVariable(Variable $variable) {
		$id = $variable->getId();
		if (!$this->variableExists($id)) {
			$this->variables[$id] = $variable;
		}
		return $variable;
	}
	public function alterVariable($variable, $options) {
		$variable = $this->getVariable($variable);
		if ($variable !== null) {
			$variable->parseOptions($options);
			return true;
		}
		return false;
	}

	/**
	 * @param $variable
	 * @return Variable
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
	public function getVariableById($id) {
		if ($this->variableExists($id)) {
			return $this->variables[$id];
		}
		return null;
	}

	/**
	 * @param bool $order
	 * @return Variable[]
	 * @throws VariableCollectionException
	 */
	public function getVariables($order = true) {
		if ($order === true) {
			$orderedVariables = array();
			$unorderedVariables = array();
			foreach ($this->variables as $variable) {
				$orderPosition = $variable->getOrder();
				if ($orderPosition !== null) {
					if (array_key_exists($orderPosition, $orderedVariables)) {
						throw new VariableCollectionException(sprintf('Cannot order variables because position "%s" is being used more than once', $orderPosition));
					}
					$orderedVariables[$orderPosition] = $variable;
				}
				else {
					array_push($unorderedVariables, $variable);
				}
			}
			return array_merge($orderedVariables, $unorderedVariables);
		}
		return $this->variables;
	}

	public function variableExists($id) {
		return isset($this->variables[$id]);
	}
} 