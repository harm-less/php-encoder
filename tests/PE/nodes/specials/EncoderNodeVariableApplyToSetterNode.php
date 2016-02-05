<?php

namespace PE\Nodes\Specials;

use PE\Enums\ActionVariable;
use PE\Nodes\EncoderNode;
use PE\Nodes\EncoderNodeVariable;
use PE\Samples\General\Thing;
use PE\Samples\Specials\EncoderNodeVariableApplyToSetter;

class EncoderNodeVariableApplyToSetterNode extends EncoderNode {

	function __construct() {
		parent::__construct('encoder-node-variable-apply-to-setters-node', 'encoder-node-variable-apply-to-setters-node', '\\PE\\Samples\\Specials');

		$this->addVariable(new EncoderNodeVariable('node-simple', array(
			'setterAction' => array(
				'type' => EncoderNodeVariable::ACTION_TYPE_NODE,
				'method' => 'nodeSimple',
				'variables' => array(ActionVariable::SETTER_NAME)
			)
		)));

		$this->addVariable(new EncoderNodeVariable('node-full', array(
			'setterAction' => array(
				'type' => EncoderNodeVariable::ACTION_TYPE_NODE,
				'method' => 'nodeFull',
				'variables' => array(ActionVariable::SETTER_NAME, ActionVariable::SETTER_VALUE, ActionVariable::SETTER_OBJECT, ActionVariable::SETTER_PARENT)
			)
		)));

		$this->addVariable(new EncoderNodeVariable('node-without-variables', array(
			'setterAction' => array(
				'type' => EncoderNodeVariable::ACTION_TYPE_NODE,
				'method' => 'nodeWithoutVariables'
			)
		)));
		$this->addVariable(new EncoderNodeVariable('node-without-variables-empty', array(
			'setterAction' => array(
				'type' => EncoderNodeVariable::ACTION_TYPE_NODE,
				'method' => 'nodeWithoutVariables',
				'variables' => array()
			)
		)));
		$this->addVariable(new EncoderNodeVariable('node-without-variables-null', array(
			'setterAction' => array(
				'type' => EncoderNodeVariable::ACTION_TYPE_NODE,
				'method' => 'nodeWithoutVariables',
				'variables' => array()
			)
		)));

		$this->addVariable(new EncoderNodeVariable('node-unknown-variable', array(
			'setterAction' => array(
				'type' => EncoderNodeVariable::ACTION_TYPE_NODE,
				'method' => 'nodeSimple',
				'variables' => array('unknown_variable')
			)
		)));


		$this->addVariable(new EncoderNodeVariable('var'));

		$this->addVariable(new EncoderNodeVariable('object-using-setter-action', array(
			'setterAction' => array(
				'method' => 'setVar'
			)
		)));

		$this->addVariable(new EncoderNodeVariable('object-using-setter-method', array(
			'setter' => 'setVar'
		)));

		$this->addVariable(new EncoderNodeVariable('object-using-unknown-setter-method', array(
			'setterMethod' => 'unknownMethod'
		)));
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
