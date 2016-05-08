<?php

namespace PE\Nodes\Specials;

use PE\Nodes\Children\NodeChildGetter;
use PE\Nodes\Children\NodeChildSetter;
use PE\Nodes\EncoderNode;
use PE\Nodes\EncoderNodeChild;

class SingleChildNode extends EncoderNode {

	function __construct() {
		parent::__construct('singleChildren', 'singleChild', '\\PE\\Samples\\Specials');

		$thing = $this->addChildNode(new EncoderNodeChild('thing', new NodeChildSetter('setThing'), new NodeChildGetter('getThing')));
		$thing->isArray(false);
	}

}