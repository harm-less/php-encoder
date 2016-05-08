<?php

namespace PE\Nodes\Specials;

use PE\Enums\ActionVariable;
use PE\Nodes\EncoderNode;
use PE\Nodes\EncoderNodeVariable;
use PE\Samples\General\Thing;
use PE\Samples\Specials\EncoderNodeVariableApplyToSetter;
use PE\Variables\Types\NodeAccessor;
use PE\Variables\Types\ObjectSetter;
use PE\Variables\Types\PostNodeSetter;

class EncoderNodeVariableApplyToSetterNode extends EncoderNode {

	function __construct() {
		parent::__construct('encoderNodeVariableApplyToSettersNode', 'encoderNodeVariableApplyToSetterNode', '\\PE\\Samples\\Specials');

		$nodeSimple = $this->addVariable(new EncoderNodeVariable('nodeSimple'));
		$nodeSimple->postNodeSetter(new PostNodeSetter('nodeSimple', array(NodeAccessor::VARIABLE_NAME)));

		$nodeFull = $this->addVariable(new EncoderNodeVariable('nodeFull'));
		$nodeFull->postNodeSetter(new PostNodeSetter('nodeFull', array(
			NodeAccessor::VARIABLE_NAME,
			NodeAccessor::VARIABLE_VALUE,
			NodeAccessor::VARIABLE_OBJECT,
			NodeAccessor::VARIABLE_PARENT
		)));

		$nodeWithoutVariables = $this->addVariable(new EncoderNodeVariable('nodeWithoutVariables'));
		$nodeWithoutVariables->postNodeSetter(new PostNodeSetter('nodeWithoutVariables'));

		$nodeWithoutVariablesEmpty = $this->addVariable(new EncoderNodeVariable('nodeWithoutVariablesEmpty'));
		$nodeWithoutVariablesEmpty->postNodeSetter(new PostNodeSetter('nodeWithoutVariables', array()));

		$nodeWithoutVariablesNull = $this->addVariable(new EncoderNodeVariable('nodeWithoutVariablesNull'));
		$nodeWithoutVariablesNull->postNodeSetter(new PostNodeSetter('nodeWithoutVariables', array()));

		$nodeUnknownVariable = $this->addVariable(new EncoderNodeVariable('nodeUnknownVariable'));
		$nodeUnknownVariable->postNodeSetter(new PostNodeSetter('nodeSimple', array('unknown_variable')));

		$this->addVariable(new EncoderNodeVariable('var'));
	}

	public function nodeSimple($nodeData, $setterName) {
		$nodeData['copied'] = $nodeData[$setterName];
		return $nodeData;
	}
	public function nodeFull($nodeData, $setterName, $setterValue, EncoderNodeVariableApplyToSetter $object, Thing $parent) {
		$nodeData['name'] = $setterName;
		$nodeData['value'] = $setterValue;
		$nodeData['object'] = $object;
		$nodeData['parent'] = $parent;
		return $nodeData;
	}

	public function nodeWithoutVariables($nodeData) {
		$nodeData['test'] = 'altered';
		return $nodeData;
	}
}
