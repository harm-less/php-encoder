<?php

namespace PE\Nodes\Specials;

use PE\Nodes\EncoderNode;
use PE\Nodes\EncoderNodeVariable;
use PE\Samples\Specials\VariableTypes;
use PE\Variables\Types\NodeAccessor;
use PE\Variables\Types\PostNodeGetter;
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
		$required->postNodeGetter(new PostNodeGetter('postNodeRequiredGetter', array(
			NodeAccessor::VARIABLE_NODE,
			NodeAccessor::VARIABLE_NAME,
			NodeAccessor::VARIABLE_VALUE,
			NodeAccessor::VARIABLE_OBJECT,
			NodeAccessor::VARIABLE_PARENT
		)));

		$optional = $this->addVariable(new EncoderNodeVariable('optional'));
		$optional->preNodeSetter(new PreNodeSetter('preNodeOptionalSetter', array(
			NodeAccessor::VARIABLE_NODE,
			NodeAccessor::VARIABLE_NAME,
			NodeAccessor::VARIABLE_VALUE,
			NodeAccessor::VARIABLE_PARENT
		)));
		$optional->postNodeSetter(new PostNodeSetter('postNodeOptionalSetter', array(
			NodeAccessor::VARIABLE_NODE,
			NodeAccessor::VARIABLE_NAME,
			NodeAccessor::VARIABLE_VALUE,
			NodeAccessor::VARIABLE_OBJECT,
			NodeAccessor::VARIABLE_PARENT
		)));
		$optional->postNodeGetter(new PostNodeGetter('postNodeOptionalGetter', array(
			NodeAccessor::VARIABLE_NODE,
			NodeAccessor::VARIABLE_NAME,
			NodeAccessor::VARIABLE_VALUE,
			NodeAccessor::VARIABLE_OBJECT,
			NodeAccessor::VARIABLE_PARENT
		)));
	}

	public function preNodeRequiredSetter($nodeData, VariableTypesNode $variableTypesNode, $name, $value, $parent) {
		$nodeData['required'] = $nodeData['required'] . ' | setter pre';
		return $nodeData;
	}
	public function postNodeRequiredSetter($nodeData, VariableTypesNode $variableTypesNode, $name, $value, VariableTypes $variableTypes, $parent) {
		$nodeData['required'] = $nodeData['required'] . ' | setter post';
		return $nodeData;
	}
	public function postNodeRequiredGetter($nodeData, VariableTypesNode $variableTypesNode, $name, $value, VariableTypes $variableTypes, $parent) {
		print_r('post getter required');
		$nodeData['required'] = $nodeData['required'] . ' | getter post';
		return $nodeData;
	}


	public function preNodeOptionalSetter($nodeData, VariableTypesNode $variableTypesNode, $name, $value, $parent) {
		$nodeData['optional'] = $nodeData['optional'] . ' | setter  pre';
		return $nodeData;
	}
	public function postNodeOptionalSetter($nodeData, VariableTypesNode $variableTypesNode, $name, $value, VariableTypes $variableTypes, $parent) {
		$nodeData['optional'] = $nodeData['optional'] . ' | setter  post';
		return $nodeData;
	}
	public function postNodeOptionalGetter($nodeData, VariableTypesNode $variableTypesNode, $name, $value, VariableTypes $variableTypes, $parent) {
		print_r('post getter optional');
		$nodeData['optional'] = $nodeData['optional'] . ' | getter post';
		return $nodeData;
	}
}