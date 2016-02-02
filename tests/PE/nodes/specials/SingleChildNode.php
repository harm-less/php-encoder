<?php

namespace PE\Nodes\Specials;

use PE\Nodes\EncoderNode;
use PE\Nodes\EncoderNodeChild;

class SingleChildNode extends EncoderNode {

	function __construct() {
		parent::__construct('single-children', 'single-child', '\\PE\\Samples\\Specials');

		$this->addChildNode(new EncoderNodeChild('thing', array(
			'setter' => 'setThing',
			'getter' => 'getThing',
			'isArray' => false
		)));
	}

}