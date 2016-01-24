<?php

namespace PE\Nodes\Farm;

use PE\Enums\ActionVariable;
use PE\Nodes\EncoderNode;
use PE\Nodes\EncoderNodeVariable;
use PE\Samples\Farm\Building;

class BuildingNode extends EncoderNode {

	function __construct($classPrepend = null) {

		parent::__construct('buildings', 'building', $classPrepend !== null ? $classPrepend : '\\PE\\Samples\\Farm');

		$this->addVariable(new EncoderNodeVariable('type', array(
			'setterAction' => array(
				'type' => EncoderNodeVariable::ACTION_TYPE_OBJECT,
				'method' => 'setType',
				'variables' => array(ActionVariable::SETTER_VALUE, ActionVariable::SETTER_NODE_DATA)
			),
			'getterAction' => array(
				'type' => EncoderNodeVariable::ACTION_TYPE_NODE,
				'method' => 'getBuildingType',
				'variables' => array(ActionVariable::GETTER_OBJECT)
			),
			'alwaysExecute' => true
		)));
	}

	function setType(Building $building, $value, $nodeData) {
		$building->setType($value);
	}
	public function getBuildingType($nodeData, Building $building) {
		$nodeData['type'] = $building->getType();
		return $nodeData;
	}
}