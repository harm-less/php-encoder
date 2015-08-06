<?php

namespace PE\Nodes\Farm\Buildings;

use PE\Nodes\Farm\BuildingNode;

class CustomBuilding extends BuildingNode {

	function __construct() {
		parent::__construct('\\PE\\Samples\\Farm\\Buildings');
	}
}