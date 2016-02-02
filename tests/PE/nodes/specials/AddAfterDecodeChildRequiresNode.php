<?php

namespace PE\Nodes\Specials;

use PE\Enums\ActionVariable;
use PE\Nodes\EncoderNode;
use PE\Nodes\EncoderNodeChild;
use PE\Nodes\EncoderNodeVariable;
use PE\Samples\Specials\AddAfterDecodeParent;

class AddAfterDecodeChildRequiresNode extends EncoderNode {

	function __construct() {
		parent::__construct('add-after-decode-children-require', 'add-after-decode-child-require', '\\PE\\Samples\\Specials');

		$this->addVariable(new EncoderNodeVariable('name', array(
			'setterAction' => array(
				'type' => EncoderNodeChild::ACTION_TYPE_NODE,
				'method' => 'nodeSetName',
				'variables' => array(ActionVariable::SETTER_VALUE, ActionVariable::SETTER_PARENT)
			)
		)));
	}

	public function nodeSetName($nodeData, $value, AddAfterDecodeParent $parent) {

		print_r($nodeData);
		print_r($value);
		print_r($parent);
		$children = $parent->getChildren();
		print_r($children);
		//$children[0]->setName('It worked!');

		//$

		return $nodeData;
	}
}