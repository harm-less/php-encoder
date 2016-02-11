<?php

namespace PE\Samples\Farm\Buildings;

use PE\Samples\Farm\Animal;
use PE\Samples\Farm\Building;

class AnimalsBuilding extends Building {

	/**
	 * @var Animal[]
	 */
	protected $animals = array();

	function __construct($type = null) {
		parent::__construct($type);
	}

	/**
	 * @param Animal $animal
	 */
	function addAnimal(Animal $animal) {
		array_push($this->animals, $animal);
	}

	/**
	 * @return Animal[]
	 */
	function getAnimals() {
		return $this->animals;
	}

}