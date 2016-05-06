<?php

namespace PE\Nodes\Farm;

use PE\Enums\ActionVariable;
use PE\Nodes\EncoderNode;
use PE\Nodes\EncoderNodeVariable;
use PE\Samples\Farm\Building;
use PE\Variables\Types\NodeAccessor;
use PE\Variables\Types\PostNodeGetter;

class BuildingNode extends EncoderNode {

	function __construct($classPrepend = null, $nodeTypeName = null) {

		parent::__construct('buildings', 'building', $classPrepend !== null ? $classPrepend : '\\PE\\Samples\\Farm', $nodeTypeName);

		$type = $this->addVariable(new EncoderNodeVariable('type'));

		$typePostNodeGetter = $type->postNodeGetter(new PostNodeGetter('getBuildingType', array(
			NodeAccessor::VARIABLE_OBJECT
		)));
		$typePostNodeGetter->alwaysExecute(true);
	}

	public function getBuildingType($nodeData, Building $building) {
		$nodeData['type'] = $building->getType();
		return $nodeData;
	}
}