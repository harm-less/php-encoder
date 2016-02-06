<?php

namespace PE\Nodes\Farm\Buildings;

use PE\Nodes\EncoderNodeChild;

class AnimalsBuildingNode extends CustomBuilding {

	function __construct($nodeTypeName) {
		parent::__construct($nodeTypeName);

		$this->addChildNode(new EncoderNodeChild('animals', array(
			'setter' => 'addAnimal',
			'getter' => 'getAnimals'
		)));
	}

}