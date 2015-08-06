<?php

namespace PE\Samples\Farm;

class Animal {

	/**
	 * @var string
	 */
	protected $type;
	/**
	 * @var string
	 */
	protected $name;

	function __construct($name) {
		$this->setName($name);
	}

	/**
	 * @param string $type
	 */
	public function setType($type)
	{
		$this->type = $type;
	}
	/**
	 * @return string
	 */
	public function getType() {
		return $this->type !== null ? $this->type : 'animal';
	}

	/**
	 * @param string $name
	 */
	public function setName($name) {
		$this->name = $name;
	}
	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}
}