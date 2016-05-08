<?php

namespace PE\Nodes\Specials;

use PE\Nodes\Children\NodeChildGetter;
use PE\Nodes\Children\NodeChildSetter;
use PE\Nodes\EncoderNode;
use PE\Nodes\EncoderNodeChild;

class NonArrayGetterMethodOnPurposeNode extends EncoderNode {

	function __construct() {
		parent::__construct('nonArrayGetterMethodsOnPurpose', 'nonArrayGetterMethodOnPurpose', '\\PE\\Samples\\Specials');

		$things = $this->addChildNode(new EncoderNodeChild('things', new NodeChildSetter('addThing'), new NodeChildGetter('getThing')));
		$things->isArray(false);
	}
}