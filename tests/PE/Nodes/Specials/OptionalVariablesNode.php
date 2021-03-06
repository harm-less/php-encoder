<?php

namespace PE\Nodes\Specials;

use PE\Nodes\EncoderNode;
use PE\Nodes\EncoderNodeVariable;

class OptionalVariablesNode extends EncoderNode {

	function __construct() {
		parent::__construct('optionalVariables', 'optionalVariables', '\\PE\\Samples\\Specials');

		$this->addVariable(new EncoderNodeVariable('name'));
		$this->addVariable(new EncoderNodeVariable('otherVariable'));
	}
}