<?php
/**
 * Is called before the object is made during encoding
 */

namespace PE\Variables\Types;

/**
 * Class PreNodeGetter
 * @package PE\Variables\Types
 */
final class PreNodeGetter extends NodeAccessor {

	function __construct($method, $parameters = null) {
		parent::__construct($method, $parameters);
	}
}