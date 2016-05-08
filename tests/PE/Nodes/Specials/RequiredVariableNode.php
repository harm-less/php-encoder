<?php

namespace PE\Nodes\Specials;

use PE\Nodes\EncoderNode;
use PE\Nodes\EncoderNodeVariable;

class RequiredVariableNode extends EncoderNode {

	function __construct() {
		parent::__construct('required-variables', 'required-variable', '\\PE\\Samples\\Specials');

		$thing = $this->addVariable(new EncoderNodeVariable('required'));
		$thing->getObjectSetter()->required(true);
	}
}