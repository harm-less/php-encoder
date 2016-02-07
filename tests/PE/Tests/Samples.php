<?php

namespace PE\Tests;

use PE\Nodes\EncoderNode;
use PE\Nodes\Erroneous\NonArrayGetterMethodNode;
use PE\Nodes\Erroneous\NoVariableGetterMethodNode;
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
use PE\Nodes\General\ThingsNode;
use PE\Nodes\Specials\AccessorMethodActionTypeNodeNode;
use PE\Nodes\Specials\AddAfterDecodeChildNode;
use PE\Nodes\Specials\AddAfterDecodeChildRequiresNode;
use PE\Nodes\Specials\AddAfterDecodeParentNode;
use PE\Nodes\Specials\ClassLoaderNode;
use PE\Nodes\Specials\EncoderNodeVariableApplyToSetterNode;
use PE\Nodes\Specials\NonArrayGetterMethodOnPurposeNode;
use PE\Nodes\Specials\RequiredConstructorVariablesNode;
use PE\Nodes\Specials\SingleChildNode;
use PE\Samples\Erroneous\NonArrayGetterMethod;
use PE\Samples\Erroneous\NoVariableGetterMethod;
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
use PE\Samples\General\Things;
use PE\Samples\Specials\EncoderNodeVariableApplyToSetter;
use PE\Samples\Specials\NonArrayGetterMethodOnPurpose;
use PE\Samples\Specials\AccessorMethodActionTypeNode;
use PE\Samples\Specials\SingleChild;

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

	public function addHouseNodes() {
		$this->addBuildingNode();
		$this->addBuildingHouseNode();
		$this->addAnimalNodes(false);
		$this->addAnimalCatNode();
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
		return EncoderNode::addNode(new FarmNode());
	}

	public function addBuildingNode() {
		return EncoderNode::addNode(new BuildingNode());
	}
	public function getBuildingHouse() {
		return new House();
	}
	public function addBuildingHouseNode() {
		return EncoderNode::addNodeType(new HouseNode());
	}
	public function addBuildingGreenhouseNode() {
		return EncoderNode::addNodeType(new GreenhouseNode());
	}
	public function addBuildingBarnNode() {
		return EncoderNode::addNodeType(new BarnNode());
	}

	public function addAnimalNodes($children = true) {
		$animalNode = EncoderNode::addNode(new AnimalNode());
		if ($children) {
			$this->addAnimalCatNode();
			$this->addAnimalChickenNode();
			$this->addAnimalCowNode();
			$this->addAnimalSheepNode();
		}
		return $animalNode;
	}

	public function addAnimalCatNode() {
		return EncoderNode::addNodeType(new CatNode());
	}
	public function addAnimalChickenNode() {
		return EncoderNode::addNodeType(new ChickenNode());
	}
	public function addAnimalCowNode() {
		return EncoderNode::addNodeType(new CowNode());
	}
	public function addAnimalSheepNode() {
		return EncoderNode::addNodeType(new SheepNode());
	}


	public function getThings() {
		return new Things();
	}
	public function addThingsNode() {
		return EncoderNode::addNode(new ThingsNode());
	}
	public function getThing() {
		return new Thing();
	}
	public function addThingNode() {
		return EncoderNode::addNode(new ThingNode());
	}


	public function getNoGetterMethod() {
		return new NoGetterMethod();
	}
	public function addNoGetterMethodNode() {
		return EncoderNode::addNode(new NoGetterMethodNode());
	}

	public function getVariableNoGetterMethod() {
		return new NoVariableGetterMethod();
	}
	public function addVariableNoGetterMethodNode() {
		return EncoderNode::addNode(new NoVariableGetterMethodNode());
	}

	public function getNonArrayGetterMethod() {
		return new NonArrayGetterMethod();
	}
	public function addNonArrayGetterMethodNode() {
		return EncoderNode::addNode(new NonArrayGetterMethodNode());
	}


	public function getNonArrayGetterMethodOnPurpose() {
		return new NonArrayGetterMethodOnPurpose();
	}
	public function addNonArrayGetterMethodOnPurposeNode() {
		return EncoderNode::addNode(new NonArrayGetterMethodOnPurposeNode());
	}


	public function getSingleChild() {
		return new SingleChild();
	}
	public function addSingleChildNode() {
		return EncoderNode::addNode(new SingleChildNode());
	}


	public function getSetterMethodActionTypeNode() {
		return new AccessorMethodActionTypeNode();
	}
	public function getAccessorMethodActionTypeNodeNode() {
		return new AccessorMethodActionTypeNodeNode();
	}
	public function addAccessorMethodActionTypeNodeNode() {
		return EncoderNode::addNode(new AccessorMethodActionTypeNodeNode());
	}

	public function getEncoderNodeVariableApplyToSetter() {
		return new EncoderNodeVariableApplyToSetter();
	}
	public function getEncoderNodeVariableApplyToSetterNode() {
		return new EncoderNodeVariableApplyToSetterNode();
	}
	public function addEncoderNodeVariableApplyToSetterNodeNode() {
		return EncoderNode::addNode(new EncoderNodeVariableApplyToSetterNode());
	}

	public function addRequiredConstructorVariablesNode($addVariables = true) {
		return EncoderNode::addNode(new RequiredConstructorVariablesNode($addVariables));
	}

	public function addClassLoaderNode($setupLoader) {
		return EncoderNode::addNode(new ClassLoaderNode($setupLoader));
	}

	public function addAddAfterDecodeNodes($addAfterAttributes = true) {
		EncoderNode::addNode(new AddAfterDecodeParentNode($addAfterAttributes));
		EncoderNode::addNode(new AddAfterDecodeChildNode());
		EncoderNode::addNode(new AddAfterDecodeChildRequiresNode());
	}
}
