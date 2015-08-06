<?php

namespace PE\Nodes\Farm;

use PE\Nodes\EncoderNode;
use PE\Nodes\EncoderNodeVariable;

class AnimalNode extends EncoderNode {

	function __construct($classPrepend = null) {
		parent::__construct('animals', 'animal', $classPrepend !== null ? $classPrepend : '\\PE\\Samples\\Farm');

		$this->addVariable(new EncoderNodeVariable('type'));
		$this->addVariable(new EncoderNodeVariable('name'));
	}

}