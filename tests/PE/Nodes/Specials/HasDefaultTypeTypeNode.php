<?php

namespace PE\Nodes\Specials;

class HasDefaultTypeTypeNode extends HasDefaultTypeNode {

	function __construct() {
		parent::__construct(HasDefaultTypeNode::NEW_DEFAULT_TYPE);
	}
}