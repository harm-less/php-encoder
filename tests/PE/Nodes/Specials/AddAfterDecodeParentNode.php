<?php

namespace PE\Nodes\Specials;

use PE\Nodes\Children\NodeChildGetter;
use PE\Nodes\Children\NodeChildSetter;
use PE\Nodes\EncoderNode;
use PE\Nodes\EncoderNodeChild;
use PE\Nodes\EncoderNodeVariable;

class AddAfterDecodeParentNode extends EncoderNode {

	function __construct($addAfterAttributes = true) {
		parent::__construct('addAfterDecodeParents', 'addAfterDecodeParent', '\\PE\\Samples\\Specials');

		$this->addVariable(new EncoderNodeVariable('name'));

		$addAfterDecodeChildSetter = new NodeChildSetter('addChild');
		$addAfterDecodeChildSetter->setAfterChildren(false);
		$addAfterDecodeChildSetter->setAfterAttributes($addAfterAttributes);
		$this->addChildNode(new EncoderNodeChild('addAfterDecodeChild', $addAfterDecodeChildSetter, new NodeChildGetter('getChildren')));

		$this->addChildNode(new EncoderNodeChild('addAfterDecodeChildrenRequire', new NodeChildSetter('addChildRequires'), new NodeChildGetter('getChildrenRequires')));
	}
}