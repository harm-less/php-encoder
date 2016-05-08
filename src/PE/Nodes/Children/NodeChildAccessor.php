<?php

namespace PE\Nodes\Children;

abstract class NodeChildAccessor {

	private $method;

	function __construct($method) {
		$this->method = $method;
	}

	public function getMethod() {
		return $this->method;
	}

}

?>