<?php

//todo Cache variables

include 'functions-bootstrap.php';


// Load our autoloader, and add our Test class namespace
$autoloader = require(dirname(__DIR__) . '/vendor/autoload.php');

use \PE\Nodes\EncoderNode;

EncoderNode::addNode(new \PE\Nodes\FarmNode());

EncoderNode::addNode(new \PE\Nodes\Farm\BuildingNode());
EncoderNode::addNodeType(new \PE\Nodes\Farm\Buildings\HouseNode(), 'house');
EncoderNode::addNodeType(new \PE\Nodes\Farm\Buildings\GreenhouseNode(), 'greenhouse');
EncoderNode::addNodeType(new \PE\Nodes\Farm\Buildings\BarnNode(), 'barn');

EncoderNode::addNode(new \PE\Nodes\Farm\AnimalNode());
EncoderNode::addNodeType(new \PE\Nodes\Farm\Animals\CatNode(), 'cat');
EncoderNode::addNodeType(new \PE\Nodes\Farm\Animals\ChickenNode(), 'chicken');
EncoderNode::addNodeType(new \PE\Nodes\Farm\Animals\CowNode(), 'cow');
EncoderNode::addNodeType(new \PE\Nodes\Farm\Animals\SheepNode(), 'sheep');