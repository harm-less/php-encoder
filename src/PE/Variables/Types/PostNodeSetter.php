<?php
/**
 * Is called after the object is made during decoding
 */

namespace PE\Variables\Types;

/**
 * Class PostNodeSetter
 * @package PE\Variables\Types
 */
final class PostNodeSetter extends NodeAccessor {

	function __construct($method, $parameters = null) {
		parent::__construct($method, $parameters);
	}
}