<?php

namespace PE\Nodes\Erroneous;

use PE\Nodes\EncoderNode;
use PE\Nodes\EncoderNodeVariable;

class NoVariableGetterMethodNode extends EncoderNode {

	function __construct() {
		parent::__construct('noGetterMethods', 'noGetterMethod', '\\PE\\Samples\\Erroneous');

		$this->addVariable(new EncoderNodeVariable('nonExistent'));
	}

}