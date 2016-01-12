<?php

namespace PE\Nodes\Erroneous;

use PE\Nodes\EncoderNode;
use PE\Nodes\EncoderNodeChild;

class NonArrayGetterMethodNode extends EncoderNode {

	function __construct() {
		parent::__construct('non-array-getter-methods', 'non-array-getter-method', '\\PE\\Samples\\Erroneous');

		$this->addChildNode(new EncoderNodeChild('things', array(
			'setter' => 'addThing',
			'getter' => 'getThings'
		)));
	}

}