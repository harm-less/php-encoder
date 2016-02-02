<?php

namespace PE\Nodes\Specials;

use PE\Nodes\EncoderNode;
use PE\Nodes\EncoderNodeChild;

class NonArrayGetterMethodOnPurposeNode extends EncoderNode {

	function __construct() {
		parent::__construct('non-array-getter-methods-on-purpose', 'non-array-getter-method-on-purpose', '\\PE\\Samples\\Specials');

		$this->addChildNode(new EncoderNodeChild('things', array(
			'setter' => 'addThing',
			'getter' => 'getThing',
			'isArray' => false
		)));
	}

}