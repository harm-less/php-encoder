<?php

namespace PE\Tests\Options;
use PE\Options\EncoderOptions;
use PE\Tests\Samples;

class EncoderOptionsTest extends Samples
{

	protected function setUp() {
		parent::setUp();
		$this->_peApp = $this->encoderOptions(array());
	}

	/**
	 * @param $options
	 * @return EncoderOptions
	 */
	protected function encoderOptions($options) {
		return new EncoderOptions($options);
	}

	public function testConstructor() {
		$options = new EncoderOptions(array());
		$this->assertNotNull($options);
		$this->assertTrue($options instanceof EncoderOptions);
		$this->assertEquals(array(), $options->getRawOptions());
	}

	public function testGetRawOptions() {
		$options = $this->encoderOptions(array(
			'hello' => 'test'
		));
		$this->assertEquals(array(
			'hello' => 'test'
		), $options->getRawOptions());
	}

	public function testSetOptions() {
		$options = $this->encoderOptions(array(
			'init' => 'hello'
		));
		$this->assertEquals('hello', $options->option('init'));
		$options->setOptions(array(
			'init' => 'world'
		));
		$this->assertEquals('world', $options->option('init'));

		$options->setOptions(array(
			'nodeInit' => 'hello world'
		), 'node');
		$this->assertEquals(array(
			'init' => 'world',
			'node' => array(
				'nodeInit' => 'hello world'
			)
		), $options->getRawOptions());
		$this->assertEquals('hello world', $options->option('nodeInit', 'node'));

		// edit node and retrieve data
		$options->setOptions(array(
			'nodeInit' => 'hello universe'
		), 'node');
		$this->assertEquals(array(
			'init' => 'world',
			'node' => array(
				'nodeInit' => 'hello universe'
			)
		), $options->getRawOptions());
		$this->assertEquals('hello universe', $options->option('nodeInit', 'node'));
	}

	public function testOption() {
		$options = $this->encoderOptions(array(
			'option' => 'test',
			'rootOption' => 'root',
			'node' => array(
				'option' => 'nodeOption'
			),
			'node[1]' => array(
				'option' => 'nodeOption1'
			),
			'things' => array(
				'option' => 'thingOption'
			)
		));
		$this->assertEquals('test', $options->option('option'));
		$this->assertNull($options->option('unknown'));
		$this->assertEquals('nodeOption', $options->option('option', 'node'));
		$this->assertEquals('test', $options->option('option', 'unknown'));

		// inheritance
		$this->assertEquals('root', $options->option('rootOption'));
		$this->assertEquals('root', $options->option('rootOption', 'node'));

		// iterated
		$this->assertEquals('nodeOption1', $options->option('option', 'node[1]'));

		$node = $this->addThingNode();
		$this->assertEquals('thingOption', $options->option('option', $node));
	}

	public function testHasOption() {
		$options = $this->encoderOptions(array(
			'option' => 'test',
			'node' => array(
				'option' => 'option',
				'nodeOption' => 'nodeOption'
			)
		));
		$this->assertTrue($options->hasOption('option'));
		$this->assertTrue($options->hasOption('option', 'node'));
		$this->assertTrue($options->hasOption('nodeOption', 'node'));
		$this->assertTrue($options->hasOption('option', 'unknown'));

		$this->assertFalse($options->hasOption('unknown'));
	}

	public function testGetRootOptions() {
		$options = $this->encoderOptions(array(
			'option' => 'test',
			'node' => array(
				'option' => 'nodeOption'
			)
		));
		$this->assertEquals(array(
			'option' => 'test'
		), $options->getRootOptions());
	}

	public function testNodeExists() {
		$this->addThingsNode();
		$options = $this->encoderOptions(array(
			'thingsContainer' => array(),
			'things' => array()
		));
		$this->assertTrue($options->nodeExists('thingsContainer'));
		$this->assertFalse($options->nodeExists('things'));
		$this->assertFalse($options->nodeExists('unknown'));
	}
	public function testRawNodeExists() {
		$options = $this->encoderOptions(array(
			'node' => array()
		));
		$this->assertTrue($options->rawNodeExists('node'));
		$this->assertFalse($options->rawNodeExists('unknown'));
	}
	public function testGetRawNode() {
		$options = $this->encoderOptions(array(
			'node' => array(
				'option' => 'hello world'
			)
		));
		$this->assertEquals(array(
			'option' => 'hello world'
		), $options->getRawNode('node'));
	}

	public function testGetRawNodeWithUnknownNodeName() {
		$this->setExpectedException('\\PE\\Exceptions\\EncoderOptionsException', 'Cannot get raw node "unknown" because it doesn\'t exist');
		$options = $this->encoderOptions(array());
		$options->getRawNode('unknown');
	}

	public function testProcessOptionsFromNode() {
		$thingsNode = $this->addThingsNode();
		$options = $this->encoderOptions(array(
			'root' => 'rootOption',
			'thingsContainer' => array(
				'option' => 'hello',
				'thingsContainerOption' => 'options'
			),
			'thingContainer' => array(
				'option' => 'world',
				'thingContainerOption' => 'optionSingle'
			)
		));
		$this->assertEquals(array(
			'root' => 'rootOption',
			'option' => 'hello',
			'thingContainerOption' => 'optionSingle',
			'thingsContainerOption' => 'options'
		), $options->processOptionsFromNode($thingsNode));
	}
}