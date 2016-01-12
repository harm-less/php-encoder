<?php

namespace PE\Nodes\Erroneous;

use PE\Nodes\EncoderNode;
use PE\Nodes\EncoderNodeChild;

class NoGetterMethodNode extends EncoderNode {

	function __construct() {
		parent::__construct('no-getter-methods', 'no-getter-method', '\\PE\\Samples\\Erroneous');

		$this->addChildNode(new EncoderNodeChild('things', array(
			'setter' => 'addThing',
			'getter' => 'getThings'
		)));
	}

}