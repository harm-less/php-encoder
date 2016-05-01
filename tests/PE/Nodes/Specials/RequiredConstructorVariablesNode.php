<?php

namespace PE\Nodes\Specials;

use PE\Nodes\EncoderNode;
use PE\Nodes\EncoderNodeVariable;

class RequiredConstructorVariablesNode extends EncoderNode {

	function __construct($addVariables = true) {
		parent::__construct('required-constructors-variables', 'required-constructor-variables', '\\PE\\Samples\\Specials');

		if ($addVariables === true) {
			$this->addVariable(new EncoderNodeVariable('name'));
			$this->addVariable(new EncoderNodeVariable('variable-category'));
		}
	}
}