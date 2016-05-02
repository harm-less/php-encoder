<?php
/**
 * Variable/attribute of a EncoderNode
 */

namespace PE\Variables;

use PE\Exceptions\VariableException;

/**
 * Class Variable
 * @package PE\Variables
 */
class Variable {

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

	/**
	 * @param string|null $id Id used in collection
	 */
	function __construct($id = null) {
		$this->setId($id);
	}

	/**
	 * @param string $id
	 * @return null|string
	 */
	public function setId($id) {
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
}