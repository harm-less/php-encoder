<?php

namespace PE\Tests;

use PE\Samples\Farm;
use PE\Samples\Farm\Animals\Cow;
use PE\Samples\Farm\Buildings\Barn;
use PE\Samples\Farm\Buildings\Greenhouse;
use PE\Samples\Farm\Buildings\House;
use PE\Samples\Farm\Animals\Cat;
use PE\Samples\Farm\Animals\Sheep;
use PE\Samples\Farm\Animals\Chicken;

class Samples extends AbstractPETest
{
	function __construct() {

	}

	/**
	 * @return House
	 */
	public function getHouse() {
		$cat = new Cat();

		$house = new House();
		$house->addAnimal($cat);

		return $house;
	}

	/**
	 * @return Farm
	 */
	public function getFarm() {
		$cow1 = new Cow();
		$cow2 = new Cow();
		$chicken1 = new Chicken();
		$chicken2 = new Chicken();
		$chicken3 = new Chicken();
		$sheep1 = new Sheep();
		$sheep2 = new Sheep();

		$greenHouse = new Greenhouse();

		$barn = new Barn();
		$barn->addAnimal($cow1);
		$barn->addAnimal($cow2);
		$barn->addAnimal($chicken1);
		$barn->addAnimal($chicken2);
		$barn->addAnimal($chicken3);
		$barn->addAnimal($sheep1);
		$barn->addAnimal($sheep2);

		$farm = $this->farm = new Farm();
		$farm->addBuilding($this->getHouse());
		$farm->addBuilding($greenHouse);
		$farm->addBuilding($barn);

		return $farm;
	}
}
