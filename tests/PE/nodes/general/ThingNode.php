<?php

namespace PE\Nodes\General;

use PE\Nodes\EncoderNode;
use PE\Nodes\EncoderNodeVariable;

class ThingNode extends EncoderNode {

	function __construct() {
		parent::__construct('things', 'thing', '\\PE\\Samples\General');

		$this->addVariable(new EncoderNodeVariable('thingVar'));
	}

}