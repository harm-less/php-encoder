<?php

namespace PE\Nodes\General;

use PE\Nodes\Children\NodeChildGetter;
use PE\Nodes\Children\NodeChildSetter;
use PE\Nodes\EncoderNode;
use PE\Nodes\EncoderNodeChild;

class ThingsNode extends EncoderNode {

	function __construct() {
		parent::__construct('thingsContainer', 'thingContainer', '\\PE\\Samples\General');

		$this->addChildNode(new EncoderNodeChild('things', new NodeChildSetter('addThing'), new NodeChildGetter('getThings')));
	}

}