<?php

namespace PE\Nodes\Farm;

use PE\Nodes\Children\NodeChildGetter;
use PE\Nodes\Children\NodeChildSetter;
use PE\Nodes\EncoderNode;
use PE\Nodes\EncoderNodeChild;

class FarmNode extends EncoderNode {

	function __construct() {
		parent::__construct('farms', 'farm', '\\PE\\Samples\\Farm');

		$buildings = $this->addChildNode(new EncoderNodeChild('buildings'));
		$buildings->setter(new NodeChildSetter('addBuilding'));
		$buildings->getter(new NodeChildGetter('getBuildings'));
	}

}