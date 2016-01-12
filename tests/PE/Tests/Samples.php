<?php

namespace PE\Tests;

use PE\Nodes\EncoderNode;
use PE\Nodes\Erroneous\NonArrayGetterMethodNode;
use PE\Nodes\Farm\AnimalNode;
use PE\Nodes\Farm\Animals\CatNode;
use PE\Nodes\Farm\Animals\ChickenNode;
use PE\Nodes\Farm\Animals\CowNode;
use PE\Nodes\Farm\Animals\SheepNode;
use PE\Nodes\Farm\BuildingNode;
use PE\Nodes\Farm\Buildings\BarnNode;
use PE\Nodes\Farm\Buildings\GreenhouseNode;
use PE\Nodes\Farm\Buildings\HouseNode;
use PE\Nodes\Farm\FarmNode;
use PE\Nodes\Erroneous\NoGetterMethodNode;
use PE\Nodes\General\ThingNode;
use PE\Nodes\Specials\NonArrayGetterMethodOnPurposeNode;
use PE\Samples\Erroneous\NonArrayGetterMethod;
use PE\Samples\Farm\Farm;
use PE\Samples\Farm\Animals\Cow;
use PE\Samples\Farm\Buildings\Barn;
use PE\Samples\Farm\Buildings\Greenhouse;
use PE\Samples\Farm\Buildings\House;
use PE\Samples\Farm\Animals\Cat;
use PE\Samples\Farm\Animals\Sheep;
use PE\Samples\Farm\Animals\Chicken;
use PE\Samples\Erroneous\NoGetterMethod;
use PE\Samples\General\Thing;
use PE\Samples\Specials\NonArrayGetterMethodOnPurpose;

class Samples extends AbstractPETest
{

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
	 * @param bool $addNodes
	 * @return Farm
	 */
	public function getFarm($addNodes = true) {
		if ($addNodes) {
			$this->addFarmNodes();
		}

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

	public function addFarmNodes() {
		$this->addFarmNode();

		$this->addBuildingNode();
		$this->addBuildingHouseNode();
		$this->addBuildingGreenhouseNode();
		$this->addBuildingBarnNode();

		$this->addAnimalNodes();
	}
	public function addFarmNode() {
		EncoderNode::addNode(new FarmNode());
	}

	public function addBuildingNode() {
		EncoderNode::addNode(new BuildingNode());
	}
	public function addBuildingHouseNode() {
		EncoderNode::addNodeType(new HouseNode(), 'house');
	}
	public function addBuildingGreenhouseNode() {
		EncoderNode::addNodeType(new GreenhouseNode(), 'greenhouse');
	}
	public function addBuildingBarnNode() {
		EncoderNode::addNodeType(new BarnNode(), 'barn');
	}

	public function addAnimalNodes() {
		EncoderNode::addNode(new AnimalNode());
		EncoderNode::addNodeType(new CatNode(), 'cat');
		EncoderNode::addNodeType(new ChickenNode(), 'chicken');
		EncoderNode::addNodeType(new CowNode(), 'cow');
		EncoderNode::addNodeType(new SheepNode(), 'sheep');
	}


	public function getThings() {
		return new Thing();
	}
	public function addThingsNode() {
		EncoderNode::addNode(new ThingNode());
	}


	public function getNoGetterMethod() {
		return new NoGetterMethod();
	}
	public function addNoGetterMethodNode() {
		EncoderNode::addNode(new NoGetterMethodNode());
	}

	public function getNonArrayGetterMethod() {
		return new NonArrayGetterMethod();
	}
	public function addNonArrayGetterMethodNode()
	{
		EncoderNode::addNode(new NonArrayGetterMethodNode());
	}


	public function getNonArrayGetterMethodOnPurpose() {
		return new NonArrayGetterMethodOnPurpose();
	}
	public function addNonArrayGetterMethodOnPurposeNode() {
		EncoderNode::addNode(new NonArrayGetterMethodOnPurposeNode());
	}
}
