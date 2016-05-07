<?php

namespace PE\Nodes\Farm\Buildings;

use PE\Nodes\Children\NodeChildGetter;
use PE\Nodes\Children\NodeChildSetter;
use PE\Nodes\EncoderNodeChild;

class AnimalsBuildingNode extends CustomBuilding {

	function __construct($nodeTypeName) {
		parent::__construct($nodeTypeName);

		$this->addChildNode(new EncoderNodeChild('animals', new NodeChildSetter('addAnimal'), new NodeChildGetter('getAnimals')));
	}

}