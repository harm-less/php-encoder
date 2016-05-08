<?php
/**
 * Is called before the object is made during decoding
 */

namespace PE\Variables\Types;

/**
 * Class PreNodeSetter
 * @package PE\Variables\Types
 */
final class PreNodeSetter extends NodeAccessor {

	function __construct($method, $parameters = null) {
		parent::__construct($method, $parameters);
	}
}