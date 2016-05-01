<?php

namespace PE\Nodes\Specials;

use PE\Enums\ActionVariable;
use PE\Nodes\EncoderNode;
use PE\Nodes\EncoderNodeVariable;

class AccessorMethodActionTypeNodeNode extends EncoderNode {

	function __construct() {
		parent::__construct('accessor-method-action-type-nodes', 'accessor-method-action-type-node', '\\PE\\Samples\\Specials');

		$this->addVariable(new EncoderNodeVariable('special'));

		$this->addVariable(new EncoderNodeVariable('node', array(
			'setterAction' => array(
				'type' => EncoderNodeVariable::ACTION_TYPE_NODE,
				'method' => 'addNodeToSpecial',
				'variables' => array(ActionVariable::SETTER_NAME)
			),
			'getterAction' => array(
				'type' => EncoderNodeVariable::ACTION_TYPE_NODE,
				'method' => 'getNodeFromSpecial',
				'variables' => array(ActionVariable::GETTER_NAME)
			)
		)));
	}

	public function addNodeToSpecial($data, $setterName) {
		$data['special'] = $data[$setterName];
		return $data;
	}

	public function getNodeFromSpecial($data, $getterName) {
		$data['special'] = $data[$getterName] . ' getter';
		return $data;
	}
}