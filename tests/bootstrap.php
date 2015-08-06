<?php

// Load our autoloader, and add our Test class namespace
$autoloader = require(dirname(__DIR__) . '/vendor/autoload.php');

use \PE\Nodes\EncoderNode;

EncoderNode::addNode(new \PE\Nodes\FarmNode());

EncoderNode::addNode(new \PE\Nodes\Farm\BuildingNode());
EncoderNode::addNodeType(new \PE\Nodes\Farm\Buildings\HouseNode(), 'house');
EncoderNode::addNodeType(new \PE\Nodes\Farm\Buildings\GreenHouseNode(), 'greenHouse');
EncoderNode::addNodeType(new \PE\Nodes\Farm\Buildings\BarnNode(), 'barn');

EncoderNode::addNode(new \PE\Nodes\Farm\AnimalNode());
EncoderNode::addNodeType(new \PE\Nodes\Farm\Animals\CatNode(), 'cat');
EncoderNode::addNodeType(new \PE\Nodes\Farm\Animals\ChickenNode(), 'chicken');
EncoderNode::addNodeType(new \PE\Nodes\Farm\Animals\CowNode(), 'cow');
EncoderNode::addNodeType(new \PE\Nodes\Farm\Animals\SheepNode(), 'sheep');


$cow1 = new \PE\Samples\Farm\Animals\Cow();
$cow2 = new \PE\Samples\Farm\Animals\Cow();
$chicken1 = new \PE\Samples\Farm\Animals\Chicken();
$chicken2 = new \PE\Samples\Farm\Animals\Chicken();
$chicken3 = new \PE\Samples\Farm\Animals\Chicken();
$sheep1 = new \PE\Samples\Farm\Animals\Sheep();
$sheep2 = new \PE\Samples\Farm\Animals\Sheep();
$cat = new \PE\Samples\Farm\Animals\Cat();

$house = new \PE\Samples\Farm\Buildings\House();
$house->addAnimal($cat);

$greenHouse = new \PE\Samples\Farm\Buildings\Greenhouse();

$barn = new \PE\Samples\Farm\Buildings\Barn();
$barn->addAnimal($cow1);
$barn->addAnimal($cow2);
$barn->addAnimal($chicken1);
$barn->addAnimal($chicken2);
$barn->addAnimal($chicken3);
$barn->addAnimal($sheep1);
$barn->addAnimal($sheep2);

$farm = new \PE\Samples\Farm();
$farm->addBuilding($house);
//$farm->addBuilding($greenHouse);
$farm->addBuilding($barn);

pr($farm);
$encode = new \PE\Encoders\XmlEncoder();
pr($encode->encode($farm));