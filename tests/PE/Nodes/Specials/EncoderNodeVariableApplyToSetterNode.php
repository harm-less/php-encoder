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
		parent::__construct('encoder-node-variable-apply-to-setters-node', 'encoder-node-variable-apply-to-setters-node', '\\PE\\Samples\\Specials');

		$nodeSimple = $this->addVariable(new EncoderNodeVariable('node-simple'));
		$nodeSimple->postNodeSetter(new PostNodeSetter('nodeSimple', NodeAccessor::VARIABLE_NAME));

		$nodeFull = $this->addVariable(new EncoderNodeVariable('node-full'));
		$nodeFull->postNodeSetter(new PostNodeSetter('nodeFull', array(
			NodeAccessor::VARIABLE_NAME,
			NodeAccessor::VARIABLE_VALUE,
			NodeAccessor::VARIABLE_OBJECT,
			NodeAccessor::VARIABLE_PARENT
		)));

		$nodeWithoutVariables = $this->addVariable(new EncoderNodeVariable('node-without-variables'));
		$nodeWithoutVariables->postNodeSetter(new PostNodeSetter('nodeWithoutVariables'));

		$nodeWithoutVariablesEmpty = $this->addVariable(new EncoderNodeVariable('node-without-variables-empty'));
		$nodeWithoutVariablesEmpty->postNodeSetter(new PostNodeSetter('nodeWithoutVariables', array()));

		$nodeWithoutVariablesNull = $this->addVariable(new EncoderNodeVariable('node-without-variables-null'));
		$nodeWithoutVariablesNull->postNodeSetter(new PostNodeSetter('nodeWithoutVariables', array()));

		$nodeUnknownVariable = $this->addVariable(new EncoderNodeVariable('node-unknown-variable'));
		$nodeUnknownVariable->postNodeSetter(new PostNodeSetter('nodeSimple', array('unknown_variable')));

		$this->addVariable(new EncoderNodeVariable('var'));

		$objectUsingSetterMethod = $this->addVariable(new EncoderNodeVariable('object-using-setter-method'));
		$objectUsingSetterMethod->objectSetter(new ObjectSetter('setVar'));

		$objectUsingUnknownSetterMethod = $this->addVariable(new EncoderNodeVariable('object-using-unknown-setter-method'));
		$objectUsingUnknownSetterMethod->objectSetter(new ObjectSetter('unknownMethod'));
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
