# php-encoder

[![Build Status](https://travis-ci.org/harm-less/php-encoder.svg?branch=master)](https://travis-ci.org/harm-less/php-encoder)

**php-encoder** is a fast & flexible encoder for PHP 5.3+

## About

This library will allow you to save a snapshot of your PHP object in various formats like XML or JSON (encoding). 
When your project has been setup properly you can reuse the snapshot by decoding it. The decoding process will return 
the same PHP object you started with.

This library is useful for you if you want to quickly find a way to save a state of your PHP object. Think about a 
configurator that allows you to customize a certain product in various ways for example.

## Getting started

1. PHP 5.3.x is required
2. Install php-encoder using [Composer](#composer-installation) (recommended) or manually
3. Set up the nodes you need for your PHP objects

## Composer Installation

1. Get [Composer](http://getcomposer.org/)
2. Require the encoder with `composer require harm-less/php-encoder`
3. Add the following to your application's main PHP file: `require 'vendor/autoload.php';`

## Included encoders

There are currently 2 encoding types available: *XML* and *JSON*.
Both can be used interchangeably as long as you have set up your nodes correctly.

## Example

*Hello World* - Obligatory hello world example

```php
<?php
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
?>
```

## Unit Testing

This project uses [PHPUnit](https://github.com/sebastianbergmann/phpunit/) as
its unit testing framework.

The tests all live in `/tests` and each test extends an abstract class
`AbstractPETest`

To test the project, simply run `composer install --dev` to download
a common version of PHPUnit with composer and run the tests from the main
directory with `phpunit` or `./vendor/bin/phpunit`

## Contributors

- [Harm van der Werf](https://github.com/harm-less)