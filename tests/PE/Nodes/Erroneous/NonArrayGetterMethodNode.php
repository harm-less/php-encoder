<?php

namespace PE\Nodes\Erroneous;

use PE\Nodes\Children\NodeChildGetter;
use PE\Nodes\Children\NodeChildSetter;
use PE\Nodes\EncoderNode;
use PE\Nodes\EncoderNodeChild;

class NonArrayGetterMethodNode extends EncoderNode {

	function __construct() {
		parent::__construct('non-array-getter-methods', 'non-array-getter-method', '\\PE\\Samples\\Erroneous');

		$this->addChildNode(new EncoderNodeChild('things', new NodeChildSetter('addThing'), new NodeChildGetter('getThings')));
	}
}