<?php

namespace PE\Nodes\Farm\Buildings;

use PE\Nodes\EncoderNodeChild;

class AnimalsBuildingNode extends CustomBuilding {

	function __construct() {
		parent::__construct();

		$this->addChildNode(new EncoderNodeChild('animals', array(
			'setter' => 'addAnimal',
			'getter' => 'getAnimals'
		)));
	}

}