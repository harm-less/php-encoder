<?php

namespace PE\Nodes\Specials;

use PE\Nodes\EncoderNode;
use PE\Nodes\EncoderNodeChild;

class AddAfterDecodeParentNode extends EncoderNode {

	function __construct() {
		parent::__construct('add-after-decode-parents', 'add-after-decode-parent', '\\PE\\Samples\\Specials');

		$this->addChildNode(new EncoderNodeChild('add-after-decode-children', array(
			'setter' => 'addChild',
			'getter' => 'getChildren',
			'setAfterChildren' => false
		)));

		$this->addChildNode(new EncoderNodeChild('add-after-decode-children-require', array(
			'setter' => 'addChildRequires',
			'getter' => 'getChildrenRequires'
		)));
	}
}