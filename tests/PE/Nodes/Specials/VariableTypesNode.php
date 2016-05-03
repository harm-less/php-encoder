<?php

namespace PE\Nodes\Specials;

use PE\Nodes\EncoderNode;
use PE\Nodes\EncoderNodeVariable;
use PE\Samples\Specials\VariableTypes;
use PE\Variables\Types\NodeAccessor;
use PE\Variables\Types\PostNodeSetter;
use PE\Variables\Types\PreNodeSetter;

class VariableTypesNode extends EncoderNode {

	function __construct() {
		parent::__construct('variable-types', 'variable-type', '\\PE\\Samples\\Specials');

		$required = $this->addVariable(new EncoderNodeVariable('required'));
		$required->setType(EncoderNodeVariable::TYPE_STRING);
		$required->preNodeSetter(new PreNodeSetter('preNodeRequiredSetter', array(
			NodeAccessor::VARIABLE_NODE,
			NodeAccessor::VARIABLE_NAME,
			NodeAccessor::VARIABLE_VALUE,
			NodeAccessor::VARIABLE_PARENT
		)));
		$required->postNodeSetter(new PostNodeSetter('postNodeRequiredSetter', array(
			NodeAccessor::VARIABLE_NODE,
			NodeAccessor::VARIABLE_NAME,
			NodeAccessor::VARIABLE_VALUE,
			NodeAccessor::VARIABLE_OBJECT,
			NodeAccessor::VARIABLE_PARENT
		)));

		$required = $this->addVariable(new EncoderNodeVariable('optional'));
		$required->preNodeSetter(new PreNodeSetter('preNodeOptionalSetter', array(
			NodeAccessor::VARIABLE_NODE,
			NodeAccessor::VARIABLE_NAME,
			NodeAccessor::VARIABLE_VALUE,
			NodeAccessor::VARIABLE_PARENT
		)));
		$required->postNodeSetter(new PostNodeSetter('postNodeOptionsSetter', array(
			NodeAccessor::VARIABLE_NODE,
			NodeAccessor::VARIABLE_NAME,
			NodeAccessor::VARIABLE_VALUE,
			NodeAccessor::VARIABLE_OBJECT,
			NodeAccessor::VARIABLE_PARENT
		)));
	}

	public function preNodeRequiredSetter($nodeData, VariableTypesNode $variableTypesNode, $name, $value, $parent) {
		pr('pre required');
		pr($nodeData);
		return $nodeData;
	}
	public function postNodeRequiredSetter($nodeData, VariableTypesNode $variableTypesNode, $name, $value, VariableTypes $variableTypes, $parent) {
		pr('post required');
		return $nodeData;
	}

	public function preNodeOptionalSetter($nodeData, VariableTypesNode $variableTypesNode, $name, $value, $parent) {
		pr('pre optional');
		pr($nodeData);
		return $nodeData;
	}
	public function postNodeOptionsSetter($nodeData, VariableTypesNode $variableTypesNode, $name, $value, VariableTypes $variableTypes, $parent) {
		pr('post optional');
		return $nodeData;
	}
}