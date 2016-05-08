<?php

namespace PE\Tests;

use PE\Encoder;
use PE\Options\EncoderOptions;
use PE\Samples\Farm\Building;
use PE\Samples\Farm\Buildings\House;
use PE\Samples\Loader\ClassLoader;
use PE\Samples\Specials\AddAfterDecodeParent;
use PE\Samples\Specials\OptionalVariables;
use PE\Samples\Specials\RequiredConstructorVariables;
use PE\Samples\Specials\AccessorMethodActionTypeNode;
use PE\Samples\Specials\SingleChild;
use PE\Samples\Specials\VariableTypes;

class EncoderTest extends Samples {

	/**
	 * @return Encoder
	 */
	protected function encoder() {
		return $this->_peApp;
	}

	public function testConstructor()
	{
		$encoder = new Encoder();
		$this->assertNotNull($encoder);
		$this->assertTrue($encoder instanceof Encoder);
	}



	public function testEncodeThenDecodeAndReEncode() {
		$this->addHouseNodes();

		$house = $this->getHouse();
		$encoded = $this->encoder()->encode($house);
		$encodedProcessed = $encoded['processed'];

		$this->assertEquals(array(
			'building' => array(
				'type' => 'house',
				'animals' => array(
					array(
						'type' => 'cat',
						'name' => 'Cat'
					)
				)
			)
		), $encodedProcessed);

		$decoded = $this->encoder()->decode($encodedProcessed);
		$this->assertArrayHasKey('building', $decoded);

		/** @var House $houseDecoded */
		$houseDecoded = $decoded['building'];
		$this->assertCount(1, $houseDecoded->getAnimals());

		$reEncodedHouse = $this->encoder()->encode($house);
		$reEncodedHouseProcessed = $reEncodedHouse['processed'];

		$this->assertEquals(array(
			'building' => array(
				'type' => 'house',
				'animals' => array(
					array(
						'type' => 'cat',
						'name' => 'Cat'
					)
				)
			)
		), $reEncodedHouseProcessed);
	}

	public function testEncodeDecodedSingleChild() {
		$this->addSingleChildNode();
		$this->addThingNode();

		$singleChild = $this->getSingleChild();
		$thing = $this->getThing();
		$thing->setThingVar('hello world');
		$singleChild->setThing($thing);

		$encoded = $this->encoder()->encode($singleChild);
		$encodedProcessed = $encoded['processed'];

		$this->assertEquals(array(
			'single-child' => array(
				'thing' => array(
					'thingVar' => 'hello world'
				)
			)
		), $encodedProcessed);

		$decoded = $this->encoder()->decode($encodedProcessed);
		$this->assertArrayHasKey('single-child', $decoded);

		/** @var SingleChild $singleChildDecoded */
		$singleChildDecoded = $decoded['single-child'];
		$this->assertNotEmpty($singleChildDecoded->getThing());
	}



	public function testDecode() {
		$this->addFarmNodes();

		$decoded = $this->encoder()->decode(array(
			'building' => array(
				'type' => 'house',
				'animals' => array(
					array(
						'type' => 'cat',
						'name' => 'Cat'
					)
				)
			)
		));
		$this->assertArrayHasKey('building', $decoded);

		/** @var House $houseDecoded */
		$houseDecoded = $decoded['building'];
		$this->assertCount(1, $houseDecoded->getAnimals());
	}

	public function testDecodeWhenNodeDoesNotExist() {
		$this->setExpectedException('\\PE\\Exceptions\\EncoderException', 'Node name "unknownNodeName" is not specified');
		$this->encoder()->decode(array(
			'unknownNodeName' => array()
		));
	}
	public function testDecodeWhenNodeTypeDoesNotExist() {
		$this->setExpectedException('\\PE\\Exceptions\\EncoderException', 'Trying to decode node, but encoder type "house" in parent "buildings" is not found. Make sure it has been loaded.');
		$this->addBuildingNode();
		$this->encoder()->decode(array(
			'building' => array(
				'type' => 'house'
			)
		));
	}
	public function testDecodeWhenChildNodeTypeDoesNotExist() {
		$this->setExpectedException('\\PE\\Exceptions\\EncoderException', 'Trying to decode node, but child node "unknownVariable" doesn\'t seem to be configured or you are trying to save an array into an attribute which is illegal (imploded value: ""). Make sure you either register this node or to encode the value to something other than an array.');
		$this->addBuildingNode();
		$this->encoder()->decode(array(
			'building' => array(
				'unknownVariable' => array()
			)
		));
	}

	public function testDecodeWithCamelCaseVariableName() {
		$this->addThingNode();
		$this->assertNotEmpty($encodedThings = $this->encoder()->decode(array(
			'thing' => array(
				'thingVar' => 'Hello world'
			)
		)));
	}

	public function testDecodeSetterMethodActionTypeNode() {
		$this->addAccessorMethodActionTypeNodeNode();

		$decoded = $this->encoder()->decode(array(
			'accessor-method-action-type-node' => array(
				'special' => 'value',
				'node' => 'hello world'
			)
		));
		/** @var AccessorMethodActionTypeNode $obj */
		$obj = $decoded['accessor-method-action-type-node'];
		$this->assertEquals('hello world', $obj->getSpecial());
	}

	public function testDecodeClassLoaderWhenSetupLoaderIsSetToFalse() {
		$this->setExpectedException('\\PE\\Exceptions\\EncoderException', 'Tried loading class "\PE\Samples\Loader\ClassLoader" so it can be decoded, this failed however because it\'s not available. You either mistyped the name of the class in the node or the "loadObject()" method didn\'t load the correct file with the class');
		$this->addClassLoaderNode(false);
		$this->encoder()->decode(array(
			'class-loader' => array()
		));
	}
	public function testDecodeClassLoader() {
		$this->addClassLoaderNode(true);

		$decoded = $this->encoder()->decode(array(
			'class-loader' => array()
		));
		$obj = $decoded['class-loader'];
		$this->assertTrue(is_a($obj, '\\PE\\Samples\\Loader\\ClassLoader'));
	}

	public function testDecodeObjectWithRequiredConstructorVariablesWhenOneOfTheVariablesIsNotAvailable() {
		$this->setExpectedException('\\PE\\Exceptions\\EncoderException', 'Variable "name" for "\PE\Samples\Specials\RequiredConstructorVariables" does not exist but is required to create an object for node "required-constructor-variables" (Node type: "required-constructors-variables") at index "0"');
		$this->addRequiredConstructorVariablesNode();
		$this->encoder()->decode(array(
			'required-constructor-variables' => array()
		));
	}
	public function testDecodeObjectWithRequiredConstructorVariablesButOneRequiredVariableIsNotProperlySetup() {
		$this->setExpectedException('\\PE\\Exceptions\\EncoderException', 'Variable "name" for "\PE\Samples\Specials\RequiredConstructorVariables" is required but there is no EncoderNodeVariable available to retrieve the value for node "required-constructor-variables" (Node type: "required-constructors-variables") at index "0"');
		$encoder = $this->encoder();
		$this->addRequiredConstructorVariablesNode(false);

		$encoder->decode(array(
			'required-constructor-variables' => array(
				'name' => 'awesomeType'
			)
		));
	}
	public function testDecodeObjectWithRequiredConstructorVariables() {
		$encoder = $this->encoder();
		$this->addRequiredConstructorVariablesNode();

		$decoded = $encoder->decode(array(
			'required-constructor-variables' => array(
				'name' => 'awesomeName',
				'variableCategory' => 'awesomeCategory'
			)
		));
		/** @var RequiredConstructorVariables $obj */
		$obj = $decoded['required-constructor-variables'];
		$this->assertEquals('awesomeName', $obj->getName());
		$this->assertEquals('awesomeCategory', $obj->getVariableCategory());
		$this->assertTrue($obj->getOptional());
	}

	public function testDecodeObjectWithOptionalVariables() {
		$this->addOptionalVariablesNode();
		$decoded = $this->encoder()->decode(array(
			'optional-variables' => array(
				'name' => 'Hello world',
				'other-variable' => 'other hello world'
			)
		));
		/** @var OptionalVariables $obj */
		$obj = $decoded['optional-variables'];
		$this->assertEquals('Hello world', $obj->getName());
		$this->assertEquals('other hello world', $obj->getOtherVariable());
	}

	public function testDecodeObjectAllVariableTypes() {
		$this->addVariableTypesNode();
		$decoded = $this->encoder()->decode(array(
			'variable-type' => array(
				'required' => 'Hello world',
				'optional' => 'Hello other world'
			)
		));

		/** @var VariableTypes $obj */
		$obj = $decoded['variable-type'];
		$this->assertEquals('Hello world | setter pre', $obj->getRequired());
		$this->assertEquals('Hello other world | setter pre | setter post', $obj->getOptional());

		$encoded = $this->encoder()->encode($obj);
		$encodedProcessed = $encoded['processed'];
		$this->assertEquals('Hello world | setter pre | getter post', $encodedProcessed['variable-type']['required']);
		$this->assertEquals('getter pre', $encodedProcessed['variable-type']['pre-required']);
		$this->assertEquals('Hello other world | setter pre | setter post | required pre | optional pre | getter post', $encodedProcessed['variable-type']['optional']);
		$this->assertEquals('getter pre', $encodedProcessed['variable-type']['pre-optional']);
	}

	public function testDecodeWithSetAfterChildrenFalse() {
		$this->addAddAfterDecodeNodes();

		$decoded = $this->encoder()->decode(array(
			'add-after-decode-parent' => array(
				'add-after-decode-child' => array(
					'name' => 'child'
				),
				'add-after-decode-children-require' => array(
					array(
						'name' => 'child-require'
					)
				)
			)
		));

		/** @var AddAfterDecodeParent $specialDecoded */
		$specialDecoded = $decoded['add-after-decode-parent'];

		$child = $specialDecoded->getChild();

		$this->assertNotEmpty($child);
		$this->assertEquals('It worked!', $child->getName());
	}

	public function testDecodeWithSetAfterChildrenAndSetAfterAttributesFalse() {
		$this->addAddAfterDecodeNodes(false);

		$decoded = $this->encoder()->decode(array(
			'add-after-decode-parent' => array(
				'name' => 'parent',
				'add-after-decode-child' => array(
					'name' => 'child'
				),
				'add-after-decode-children-require' => array(
					array(
						'name' => 'child-require'
					)
				)
			)
		));

		/** @var AddAfterDecodeParent $specialDecoded */
		$specialDecoded = $decoded['add-after-decode-parent'];

		$child = $specialDecoded->getChild();

		$this->assertNotEmpty($child);
		$this->assertEquals('It worked and it has a name: parent!', $child->getName());
	}

	public function testDecodeWithSetAfterChildrenTrueAndChildrenInverted() {
		//$this->markTestSkipped(
		//	'This test cannot be created yet. setAfterChildren doesn\'t work when it has the wrong order like described below, so perhaps I need to build a sorting mechanism. Perhaps in "decodeRawToArray"?'
		//);

		$this->addAddAfterDecodeNodes(false);

		$decoded = $this->encoder()->decode(array(
			'add-after-decode-parent' => array(
				'name' => 'parent',
				'add-after-decode-children-require' => array(
					array(
						'name' => 'child-require'
					)
				),
				'add-after-decode-child' => array(
					'name' => 'child'
				)
			)
		));

		/** @var AddAfterDecodeParent $specialDecoded */
		$specialDecoded = $decoded['add-after-decode-parent'];

		$child = $specialDecoded->getChild();

		$this->assertNotEmpty($child);
		$this->assertEquals('It worked and it has a name: parent!', $child->getName());
	}








	public function testEncode() {
		$encoder = $this->encoder();
		$encodedFarm = $encoder->encode($this->getFarm());

		$this->assertArrayHasKey('processed', $encodedFarm);
		$this->assertArrayHasKey('raw', $encodedFarm);

		$rawEncoded = $encodedFarm['raw'];
		$this->assertArrayHasKey('attributes', $rawEncoded);
		$this->assertArrayHasKey('children', $rawEncoded);
		$this->assertArrayHasKey('nodeName', $rawEncoded);
	}

	public function testEncodeRaw()
	{
		$house = $this->getHouse();
		$this->addFarmNodes();
		$encoded = $this->encoder()->encode($house);

		$this->assertEquals(array(
			'processed' => array(
				'building' => array(
					'type' => 'house',
					'animals' => array(
						array(
							'type' => 'cat',
							'name' => 'Cat'
						)
					)
				)
			),
			'raw' => array(
				'attributes' => array(
					'type' => 'house'
				),
				'children' => array(
					'animals' => array(
						array(
							'attributes' => array(
								'type' => 'cat',
								'name' => 'Cat'
							),
							'children' => array(),
							'nodeName' => 'animals'
						)
					)
				),
				'nodeName' => 'buildings'
			)
		), $encoded);
	}

	public function testEncodeUnknownObject() {
		$encoder = $this->encoder();
		$this->assertNull($encoder->encode(new Noop()));
	}

	public function testEncodeWithoutChildObjectNodes() {
		$this->setExpectedException('\\PE\\Exceptions\\EncoderException', 'Cannot set the node name (buildings) of a node child because it doesn\'t exist. Please add the requested node with "EncoderNode::addNode()". Current node name "farms" with class name "PE\Nodes\Farm\FarmNode"');

		$this->addFarmNode();

		$this->encoder()->encode($this->getFarm(false));
	}
	public function testEncodeWithoutGetterMethod() {
		$this->setExpectedException('\\PE\\Exceptions\\EncoderException', 'Getter method "getThings" for node "things" does not exist in class "PE\Samples\Erroneous\NoGetterMethod"');

		$this->addNoGetterMethodNode();
		$this->addThingNode();

		$this->encoder()->encode($this->getNoGetterMethod());
	}
	public function testEncodeWithoutVariableGetterMethod() {
		$this->setExpectedException('\\PE\\Exceptions\\VariableTypeException', 'Method "getNonExistent" does not exist for class "PE\Samples\Erroneous\NoVariableGetterMethod" does not exist');

		$this->addVariableNoGetterMethodNode();

		$this->encoder()->encode($this->getVariableNoGetterMethod());
	}
	public function testEncodeWithGetterMethodReturningString() {
		$this->setExpectedException('\\PE\\Exceptions\\EncoderException', 'Children object for node "things" must be an array. EncoderNodeChilds are returning an array by default. If this behavior is not desired, turn it off using "$childNode->isArray(false)" or set "isArray" as an options to the EncoderNodeChild instance');

		$this->addNonArrayGetterMethodNode();
		$this->addThingNode();

		$this->encoder()->encode($this->getNonArrayGetterMethod());
	}
	public function testEncodeWhenChildNodeTypeDoesNotExist() {
		$this->setExpectedException('\\PE\\Exceptions\\EncoderException', 'Child node type for object "PE\Samples\Farm\Animals\Cat (child of "buildings")" for node "animals" not found');

		$house = $this->getHouse();
		$this->addBuildingNode();
		$this->addBuildingHouseNode();
		$this->addAnimalNodes(false);

		$this->encoder()->encode($house);
	}
	public function testEncodeNodeByValueButItDoesNotExist() {
		$this->setExpectedException('\\PE\\Exceptions\\EncoderException', 'Option "value" cannot be mapped to "type" because it does not exist in "things"');

		$encoder = $this->encoder();
		$things = $this->getThings();
		$thing = $this->getThing();
		$thing->setThingVar('hello world');
		$things->addThing($thing);

		$this->addThingNode();
		$this->addThingsNode();

		$encoder->encode($things, new EncoderOptions(array(
			'things' => array(
				'value' => 'type'
			)
		)));
	}

	public function testEncodeWithGetterMethodReturningNonArrayObject() {
		$this->addNonArrayGetterMethodOnPurposeNode();
		$this->addThingNode();

		$obj = $this->getNonArrayGetterMethodOnPurpose();
		$obj->addThing($this->getThing());

		$encoder = $this->encoder();
		$encoded = $encoder->encode($obj);

		$this->assertArrayHasKey('thingVar', $encoded['processed']['non-array-getter-method-on-purpose']['things']);
	}

	public function testEncodeWithWrapperName() {
		$encoder = $this->encoder();
		$encodedFarm = $encoder->encode($this->getFarm(), new EncoderOptions(array('wrapper' => 'test')));

		$this->assertArrayHasKey('test', $encodedFarm['processed']);
	}

	public function testEncodeWithoutNodeAttributes() {
		$encoder = $this->encoder();
		$encodedFarm = $encoder->encode($this->getFarm(), new EncoderOptions(array(
			'buildings' => array(
				'attributes' => false,
			)
		)));

		$this->assertArrayNotHasKey('type', $encodedFarm['processed']['farm']['buildings'][0], 'The \'type\' variable from \'buildings\' should not be there because the attributes/variables were turned off');
	}

	public function testEncodeNodeByKey() {
		$encoder = $this->encoder();
		$encodedFarm = $encoder->encode($this->getFarm(), new EncoderOptions(array(
			'buildings' => array(
				'key' => 'type',
			)
		)));

		$this->assertArrayNotHasKey('buildings', $encodedFarm['processed']['farm']);
		$this->assertArrayHasKey('house', $encodedFarm['processed']['farm']);
		$this->assertArrayHasKey('greenhouse', $encodedFarm['processed']['farm']);
		$this->assertArrayHasKey('barn', $encodedFarm['processed']['farm']);
	}

	public function testEncodeNodeByValue() {
		$encoder = $this->encoder();
		$things = $this->getThings();
		$thing = $this->getThing();
		$thing->setThingVar('hello world');
		$things->addThing($thing);

		$this->addThingNode();
		$this->addThingsNode();

		$encodedThings = $encoder->encode($things, new EncoderOptions(array(
			'things' => array(
				'value' => 'thingVar'
			)
		)));

		$this->assertTrue(is_string($encodedThings['processed']['thingContainer']['things'][0]));
		$this->assertEquals($encodedThings['processed']['thingContainer']['things'][0], 'hello world');
	}

	public function testEncodeIteratedNode() {
		$encoder = $this->encoder();
		$this->addFarmNodes();
		$encodedHouse = $encoder->encode($this->getHouse(), new EncoderOptions(array(
			'animals' => array(
				'iterate' => 2,
			)
		)));

		$this->assertArrayNotHasKey('type', $encodedHouse['processed']['building']['animals'][0]);
		$this->assertArrayHasKey(0, $encodedHouse['processed']['building']['animals'][0]);
		$this->assertCount(2, $encodedHouse['processed']['building']['animals']);
	}

	public function testEncodeIteratedNodeWithChildKey() {
		$encoder = $this->encoder();
		$this->addFarmNodes();
		$encodedHouse = $encoder->encode($this->getHouse(), new EncoderOptions(array(
			'animals' => array(
				'iterate' => 2,
				'key' => 'type'
			)
		)));

		$this->assertArrayNotHasKey('animals', $encodedHouse['processed']['building']);
		$this->assertArrayHasKey('cat', $encodedHouse['processed']['building'][0]);
	}

	public function testEncodeWithoutNodeChildren() {
		$encoder = $this->encoder();
		$encodedFarm = $encoder->encode($this->getFarm(), new EncoderOptions(array(
			'buildings' => array(
				'children' => false,
			)
		)));

		$this->assertArrayNotHasKey('animals', $encodedFarm['processed']['farm']['buildings'][0]);
		$this->assertArrayHasKey('type', $encodedFarm['processed']['farm']['buildings'][0]);
		$this->assertEmpty($encodedFarm['raw']['children']['buildings'][0]['children']);
	}
}

class Noop {
}

class UnknownBuilding extends Building {

}