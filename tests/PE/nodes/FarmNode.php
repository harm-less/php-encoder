<?php

namespace PE\Nodes;

class FarmNode extends EncoderNode {

	function __construct() {
		parent::__construct('farms', 'farm', '\\PE\\Samples');

		$this->addChildNode(new EncoderNodeChild('buildings', array(
			'setter' => 'addBuilding',
			'getter' => 'getBuildings'
		)));
	}

}