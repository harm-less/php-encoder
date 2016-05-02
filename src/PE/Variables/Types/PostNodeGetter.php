<?php
/**
 * Is called after the object is made during encoding
 */

namespace PE\Variables\Types;

/**
 * Class PostNodeGetter
 * @package PE\Variables\Types
 */
final class PostNodeGetter extends NodeAccessor {

	function __construct($method, $parameters = null) {
		parent::__construct($method, $parameters);
	}
}