<?php

namespace PE\Samples\Farm;

class Building {

	/**
	 * @var string
	 */
	protected $type;

	function __construct($type = null) {
		$this->setType($type);
	}

	/**
	 * @param string $type
	 */
	public function setType($type) {
		$this->type = $type;
	}

	/**
	 * @return string
	 */
	public function getType() {
		return $this->type !== null ? $this->type : 'building';
	}

}