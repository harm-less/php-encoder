<?php

namespace PE\Nodes\Farm;

use PE\Nodes\EncoderNode;
use PE\Nodes\EncoderNodeChild;

class FarmNode extends EncoderNode {

	function __construct() {
		parent::__construct('farms', 'farm', '\\PE\\Samples\\Farm');

		$this->addChildNode(new EncoderNodeChild('buildings', array(
			'setter' => 'addBuilding',
			'getter' => 'getBuildings'
		)));
	}

}