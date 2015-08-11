<?php

namespace PE\Tests\Encoders;

use PE\Tests\AbstractPETest;
use PE\Tests\Samples;

class CoreEncoder extends AbstractPETest {

	public function getHouse() {
		$samples = new Samples();
		return $samples->getHouse();
	}

}