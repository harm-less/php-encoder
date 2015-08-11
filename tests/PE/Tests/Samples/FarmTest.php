<?php

namespace PE\Tests\Samples;

use PE\Encoders\XmlEncoder;
use PE\Tests\Samples;

class FarmTest extends Samples {

	public function testConstructor()
	{
		$encode = new XmlEncoder();
		$xml = $encode->encode($this->getFarm());
		$encodeStrFirst = $xml->saveXML();

		$decode = $encode->decode(simplexml_import_dom($xml));
		$xml2 = $encode->encode($decode['farm']);
		$encodeStrSecond = $xml2->saveXML();

		$this->assertEquals($encodeStrFirst, $encodeStrSecond);
	}
}