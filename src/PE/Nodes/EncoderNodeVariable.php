<?php

namespace PE\Nodes;

use PE\Variables\Types\ObjectAccessor;
use PE\Variables\Types\ObjectGetter;
use PE\Variables\Types\ObjectSetter;
use PE\Variables\Types\PostNodeGetter;
use PE\Variables\Types\PostNodeSetter;
use PE\Variables\Types\PreNodeGetter;
use PE\Variables\Types\PreNodeSetter;

class EncoderNodeVariable {

	/**
	 * @var string
	 */
	private $id;
	/**
	 * @var int
	 */
	private $order;
	/**
	 * @var string
	 */
	private $type;

	/**
	 * @var PreNodeSetter
	 */
	private $preNodeSetter;

	/**
	 * @var PreNodeGetter
	 */
	private $preNodeGetter;

	/**
	 * @var PostNodeSetter
	 */
	private $postNodeSetter;

	/**
	 * @var PostNodeGetter
	 */
	private $postNodeGetter;

	/**
	 * @var ObjectSetter
	 */
	private $objectSetter;

	/**
	 * @var ObjectGetter
	 */
	private $objectGetter;


	/**
	 * Boolean variable type
	 */
	const TYPE_BOOL = 'bool';
	/**
	 * Array variable type
	 */
	const TYPE_ARRAY = 'array';
	/**
	 * String variable type
	 */
	const TYPE_STRING = 'string';

	function __construct($id, $enableObjectAccessors = true) {
		$this->setId($id);

		if ($enableObjectAccessors) {
			$this->objectSetter(new ObjectSetter());
			$this->objectGetter(new ObjectGetter());
		}
	}

	/**
	 * @param string $id
	 * @return null|string
	 */
	protected function setId($id) {
		$this->id = $id;
		return $id;
	}

	/**
	 * @return string
	 */
	public function getId() {
		return $this->id;
	}


	/**
	 * Sets the data type of the variable
	 * @param string $type
	 */
	public function setType($type) {
		$this->type = $type;
	}
	/**
	 * @return string
	 */
	public function getType() {
		return $this->type;
	}


	/**
	 * Set the order the variable (when it is a collection)
	 *
	 * A lower number means it will end up near the beginning of the array
	 * @param int $index
	 */
	public function setOrder($index) {
		$this->order = $index;
	}
	/**
	 * @return int
	 */
	public function getOrder() {
		return $this->order;
	}


	/**
	 * @param PreNodeSetter $setter
	 * @return PreNodeSetter
	 */
	public function preNodeSetter(PreNodeSetter $setter) {
		$setter->setVariable($this);
		$this->preNodeSetter = $setter;
		return $setter;
	}

	/**
	 * @return bool
	 */
	public function hasPreNodeSetter() {
		return $this->preNodeSetter !== null;
	}

	/**
	 * @return PreNodeSetter
	 */
	public function getPreNodeSetter() {
		return $this->preNodeSetter;
	}


	/**
	 * @param PreNodeGetter $getter
	 * @return PreNodeGetter
	 */
	public function preNodeGetter(PreNodeGetter $getter) {
		$getter->setVariable($this);
		$this->preNodeGetter = $getter;
		return $getter;
	}

	/**
	 * @return bool
	 */
	public function hasPreNodeGetter() {
		return $this->preNodeGetter !== null;
	}

	/**
	 * @return PreNodeGetter
	 */
	public function getPreNodeGetter() {
		return $this->preNodeGetter;
	}


	/**
	 * @param PostNodeSetter $setter
	 * @return PostNodeSetter
	 */
	public function postNodeSetter(PostNodeSetter $setter) {
		$setter->setVariable($this);
		$this->postNodeSetter = $setter;
		return $setter;
	}

	/**
	 * @return bool
	 */
	public function hasPostNodeSetter() {
		return $this->postNodeSetter !== null;
	}

	/**
	 * @return PostNodeSetter
	 */
	public function getPostNodeSetter() {
		return $this->postNodeSetter;
	}


	/**
	 * @param PostNodeGetter $getter
	 * @return PostNodeGetter
	 */
	public function postNodeGetter(PostNodeGetter $getter) {
		$getter->setVariable($this);
		$this->postNodeGetter = $getter;
		return $getter;
	}

	/**
	 * @return bool
	 */
	public function hasPostNodeGetter() {
		return $this->postNodeGetter !== null;
	}

	/**
	 * @return PostNodeGetter
	 */
	public function getPostNodeGetter() {
		return $this->postNodeGetter;
	}



	/**
	 * @param ObjectSetter|null $setter
	 * @return ObjectSetter
	 */
	public function objectSetter(ObjectSetter $setter = null) {
		if ($setter) $setter->setVariable($this);
		$this->objectSetter = $setter;
		return $setter;
	}

	/**
	 * @return bool
	 */
	public function hasObjectSetter() {
		return $this->objectSetter !== null;
	}

	/**
	 * @return ObjectSetter
	 */
	public function getObjectSetter() {
		return $this->objectSetter;
	}



	/**
	 * @param ObjectGetter|null $getter
	 * @return ObjectGetter
	 */
	public function objectGetter(ObjectGetter $getter = null) {
		if ($getter) $getter->setVariable($this);
		$this->objectGetter = $getter;
		return $getter;
	}

	/**
	 * @return bool
	 */
	public function hasObjectGetter() {
		return $this->objectGetter !== null;
	}

	/**
	 * @return ObjectGetter
	 */
	public function getObjectGetter() {
		return $this->objectGetter;
	}
}