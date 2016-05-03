<?php
/**
 * Will call methods from an EncoderNode object
 */

namespace PE\Variables\Types;

use PE\Exceptions\VariableTypeException;
use PE\Nodes\EncoderNode;

/**
 * Class NodeAccessor
 * @package PE\Variables\Types
 */
abstract class NodeAccessor extends VariableType {

	protected $parameters;

	const VARIABLE_NODE_DATA = 'node_node_data';
	const VARIABLE_NODE = 'node_encoder_node';
	const VARIABLE_NAME = 'node_name';
	const VARIABLE_VALUE = 'node_value';
	const VARIABLE_OBJECT = 'node_object';
	const VARIABLE_PARENT = 'node_parent';

	const ACCESSOR_SETTER = 'setter';
	const ACCESSOR_GETTER = 'getter';

	const ORDER_PRE = 'pre';
	const ORDER_POST = 'post';

	function __construct($method, $parameters = null) {
		parent::__construct($method);

		$this->setParameters($parameters);
	}

	protected function setParameters($parameters) {
		if (!is_array($parameters)) {
			$parameters = array();
		}
		$this->parameters = $parameters;
	}
	public function getParameters() {
		return $this->parameters;
	}


	/**
	 * Calls a certain node and provides any variables the method requires
	 *
	 * @param array $options Associated array based on all "SETTER_*" constants from ActionVariable.
	 * @return mixed Returns whatever the object returns
	 *
	 * @see ActionVariable All "SETTER_*" constants can be a key of the $parameters array
	 */
	public function apply($options) {
		$parameters = $this->getParameters();

		if (!count($parameters)) {
			// if there are no custom parameters at least send the value of the variable
			$parameters = array(self::VARIABLE_VALUE);
		}

		// prepend the required node data as the first variable
		array_unshift($parameters, self::VARIABLE_NODE_DATA);

		// using those parameters, call the node action method
		return $this->_callNodeAction($parameters[self::VARIABLE_NODE], $this->getMethod(), $this->_gatherAccessorParameters($options, $parameters));
	}

	/**
	 * Gathers all required variables
	 *
	 * @param array $options Associated array based on all "SETTER_*" constants from ActionVariable.
	 * @param array $parameters All the options I would like to extract from the $options parameter
	 * @return array Returns an array with all required parameters extracted from the $parameters variable in the
	 * right order.
	 * @see ActionVariable All "SETTER_*" constants can be a key of the $parameters array
	 */
	protected function _gatherAccessorParameters($options, $parameters) {
		$actionVariables = array();
		foreach ($parameters as $parameter) {
			if (!isset($options[$parameter])) {
				throw new VariableTypeException(sprintf('Action variable id "%s" is not known', $parameter));
			}
			array_push($actionVariables, $options[$parameter]);
		}
		return $actionVariables;
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
}