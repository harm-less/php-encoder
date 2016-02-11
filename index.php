<?php
error_reporting( E_ALL );
ini_set('display_errors', 'On');

require('vendor/autoload.php');

use PE\Encoders\XmlEncoder;

use PE\Nodes\EncoderNode;
use PE\Nodes\EncoderNodeVariable;

// create a simple class with 1 variable and a setter/getter
class HelloWorld {

	private $foo;

	public function setFoo($value) {
		$this->foo = $value;
	}
	public function getFoo() {
		return $this->foo;
	}
}

// create a corresponding node and add the variable
class HelloWorldNode extends EncoderNode {

	function __construct() {
		parent::__construct('hello-worlds', 'hello-world', null);

		$this->addVariable(new EncoderNodeVariable('foo'));
	}
}

// register the node so it becomes known to the encoder
EncoderNode::addNode(new HelloWorldNode());

// create a HelloWorld object
$helloWorld = new HelloWorld();
$helloWorld->setFoo('hello world');

// make an instance of an encoder type and encode the object
$encoder = new XmlEncoder();
$encodedResultXml = $encoder->encode($helloWorld);

// will output:
/* <?xml version="1.0" encoding="UTF-8"?>
 * <encoded>
 *   <hello-world foo="hello world"/>
 * </encoded>
 */
echo htmlentities($encodedResultXml->saveXML());

// decode the XML again
$decoded = $encoder->decode($encodedResultXml->saveXML());

// will output:
/*
 * HelloWorld Object
 * (
 *   [foo:HelloWorld:private] => hello world
 * )
 */
print_r($decoded['hello-world']);

