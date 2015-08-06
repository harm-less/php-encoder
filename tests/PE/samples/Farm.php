<?php

namespace PE\Samples;

use PE\Samples\Farm\Building;

class Farm {

	/**
	 * @var Building[]
	 */
	protected $buildings = array();

	function __construct() {

	}

	/**
	 * @param Building $building
	 * @return Building
	 */
	function addBuilding(Building $building) {
		array_push($this->buildings, $building);
		return $building;
	}

	/**
	 * @return Building[]
	 */
	function getBuildings() {
		return $this->buildings;
	}

}