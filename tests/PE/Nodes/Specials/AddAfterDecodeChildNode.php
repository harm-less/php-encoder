<?php

namespace PE\Nodes\Specials;

use PE\Nodes\EncoderNode;
use PE\Nodes\EncoderNodeVariable;

class AddAfterDecodeChildNode extends EncoderNode {

	function __construct() {
		parent::__construct('addAfterDecodeChildren', 'addAfterDecodeChild', '\\PE\\Samples\\Specials');

		$this->addVariable(new EncoderNodeVariable('name'));
	}
}