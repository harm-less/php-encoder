<?php

namespace PE\Nodes\Specials;

use PE\Nodes\EncoderNode;
use PE\Nodes\EncoderNodeChild;
use PE\Nodes\EncoderNodeVariable;

class AddAfterDecodeParentNode extends EncoderNode {

	function __construct($addAfterAttributes = true) {
		parent::__construct('add-after-decode-parents', 'add-after-decode-parent', '\\PE\\Samples\\Specials');

		$this->addVariable(new EncoderNodeVariable('name'));

		$this->addChildNode(new EncoderNodeChild('add-after-decode-child', array(
			'setter' => 'addChild',
			'getter' => 'getChildren',
			'setAfterChildren' => false,
			'setAfterAttributes' => $addAfterAttributes
		)));

		$this->addChildNode(new EncoderNodeChild('add-after-decode-children-require', array(
			'setter' => 'addChildRequires',
			'getter' => 'getChildrenRequires'
		)));
	}
}