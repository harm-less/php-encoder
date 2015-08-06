<?php

namespace PE\Nodes\Farm;

use PE\Nodes\EncoderNode;
use PE\Nodes\EncoderNodeVariable;

class BuildingNode extends EncoderNode {

	function __construct($classPrepend = null) {

		parent::__construct('buildings', 'building', $classPrepend !== null ? $classPrepend : '\\PE\\Samples\\Farm');

		$this->addVariable(new EncoderNodeVariable('type'));
	}

}