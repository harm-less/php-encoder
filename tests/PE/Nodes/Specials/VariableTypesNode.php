<?php

namespace PE\Nodes\Specials;

use PE\Nodes\EncoderNode;
use PE\Nodes\EncoderNodeVariable;
use PE\Samples\Specials\VariableTypes;
use PE\Variables\Types\NodeAccessor;
use PE\Variables\Types\PostNodeGetter;
use PE\Variables\Types\PostNodeSetter;
use PE\Variables\Types\PreNodeGetter;
use PE\Variables\Types\PreNodeSetter;

class VariableTypesNode extends EncoderNode {

	function __construct() {
		parent::__construct('variableTypes', 'variableType', '\\PE\\Samples\\Specials');

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

		$required->preNodeGetter(new PreNodeGetter('preNodeRequiredGetter', array(
			NodeAccessor::VARIABLE_NODE,
			NodeAccessor::VARIABLE_NAME,
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

		$optional->preNodeGetter(new PreNodeGetter('preNodeOptionalGetter', array(
			NodeAccessor::VARIABLE_NODE,
			NodeAccessor::VARIABLE_NAME,
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

	public function preNodeRequiredGetter($nodeData, VariableTypesNode $variableTypesNode, $name, VariableTypes $variableTypes, $parent) {
		$variableTypes->setOptional($variableTypes->getOptional() . ' | required pre');
		$nodeData['pre-required'] = 'getter pre';
		return $nodeData;
	}
	public function postNodeRequiredGetter($nodeData, VariableTypesNode $variableTypesNode, $name, $value, VariableTypes $variableTypes, $parent) {
		$nodeData['required'] = $nodeData['required'] . ' | getter post';
		return $nodeData;
	}


	public function preNodeOptionalSetter($nodeData, VariableTypesNode $variableTypesNode, $name, $value, $parent) {
		$nodeData['optional'] = $nodeData['optional'] . ' | setter pre';
		return $nodeData;
	}
	public function postNodeOptionalSetter($nodeData, VariableTypesNode $variableTypesNode, $name, $value, VariableTypes $variableTypes, $parent) {
		$nodeData['optional'] = $nodeData['optional'] . ' | setter post';
		return $nodeData;
	}

	public function preNodeOptionalGetter($nodeData, VariableTypesNode $variableTypesNode, $name, VariableTypes $variableTypes, $parent) {
		$variableTypes->setOptional($variableTypes->getOptional() . ' | optional pre');
		$nodeData['pre-optional'] = 'getter pre';
		return $nodeData;
	}
	public function postNodeOptionalGetter($nodeData, VariableTypesNode $variableTypesNode, $name, $value, VariableTypes $variableTypes, $parent) {
		$nodeData['optional'] = $nodeData['optional'] . ' | getter post';
		return $nodeData;
	}
}