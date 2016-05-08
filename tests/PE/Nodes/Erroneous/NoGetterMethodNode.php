<?php

namespace PE\Nodes\Erroneous;

use PE\Nodes\Children\NodeChildGetter;
use PE\Nodes\Children\NodeChildSetter;
use PE\Nodes\EncoderNode;
use PE\Nodes\EncoderNodeChild;

class NoGetterMethodNode extends EncoderNode {

	function __construct() {
		parent::__construct('noGetterMethods', 'noGetterMethod', '\\PE\\Samples\\Erroneous');

		$this->addChildNode(new EncoderNodeChild('things', new NodeChildSetter('addThing'), new NodeChildGetter('getThings')));
	}

}