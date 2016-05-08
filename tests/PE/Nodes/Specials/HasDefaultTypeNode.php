<?php

namespace PE\Nodes\Specials;

use PE\Nodes\EncoderNode;
use PE\Nodes\EncoderNodeChild;

class HasDefaultTypeNode extends EncoderNode {

	const NEW_DEFAULT_TYPE = 'should-be-the-default-type';

	function __construct($nodeTypeName = null) {
		parent::__construct('hasDefaultTypes', 'hasDefaultType', '\\PE\\Samples\\Specials', $nodeTypeName);
	}

	public function getDefaultType() {
		return self::NEW_DEFAULT_TYPE;
	}
}