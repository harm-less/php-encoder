<?php

namespace PE\Nodes\Erroneous;

use PE\Nodes\EncoderNode;

class EncoderNodeLoaderNode extends EncoderNode {

	private $overrideObjectFileName;

	function __construct($overrideObjectFileName = true) {
		parent::__construct('encoderNodeLoaders', 'encoderNodeLoader', '\\PE\\Samples\\Erroneous');

		$this->overrideObjectFileName = $overrideObjectFileName;
	}

	protected function _objectFileName() {
		if ($this->overrideObjectFileName) {
			return null;
		}
		else
		{
			return parent::_objectFileName();
		}
	}
}