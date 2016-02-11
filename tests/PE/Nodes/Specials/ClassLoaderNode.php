<?php

namespace PE\Nodes\Specials;

use PE\Enums\ActionVariable;
use PE\Nodes\EncoderNode;
use PE\Nodes\EncoderNodeChild;
use PE\Nodes\EncoderNodeVariable;

class ClassLoaderNode extends EncoderNode {

	private $setupLoader;

	function __construct($setupLoader) {
		parent::__construct('class-loaders', 'class-loader', '\\PE\\Samples\\Loader');

		$this->setupLoader = $setupLoader;
	}

	protected function _loadObject($object) {
		if ($this->setupLoader === true) {
			require_once TESTS_DIR . '/PE/Samples/Specials/ClassLoader.php';
		}
		return null;
	}
}