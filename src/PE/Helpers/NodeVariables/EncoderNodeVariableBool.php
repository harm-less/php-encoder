<?php

namespace PE\Helpers\NodeVariables;

use PE\Nodes\EncoderNodeVariable;
use PE\Variables\Types\ObjectGetter;
use PE\Variables\Types\ObjectSetter;

class EncoderNodeVariableBool extends EncoderNodeVariable {

	function __construct($id, $method) {
		parent::__construct($id, false);

		$this->setType(EncoderNodeVariable::TYPE_BOOL);

		$this->objectSetter(new ObjectSetter($method));
		$this->objectGetter(new ObjectGetter($method));
	}
}
?>