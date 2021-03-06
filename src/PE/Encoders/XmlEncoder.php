<?php

namespace PE\Encoders;

use LSS\Array2XML;
use PE\Encoder;
use PE\Exceptions\EncoderException;
use PE\Nodes\EncoderNode;
use PE\Nodes\EncoderNodeChild;
use PE\Options\EncoderOptions;

class XmlEncoder extends Encoder {

	const ROOT_NODE_NAME = 'encoded';

	public function encode($object, EncoderOptions $options = null) {
		$arr = parent::encode($object, $options);
		return Array2XML::createXML(self::ROOT_NODE_NAME, $arr['processed']);
	}
	protected function encodeNodeChildren(EncoderNode $node, $nodeName, EncoderNodeChild $child, $children) {
		if (!$node->isSingleNode($nodeName)) {
			return array($node->getNodeNameSingle() => $children);
		}
		return $children;
	}
	protected function encodeAttributes($array) {
		$attributes['@attributes'] = $array;
		return $attributes;
	}




	public function decode($node, EncoderOptions $options = null) {
		$node = is_string($node) ? simplexml_load_string($node) : $node;
		return parent::decode($node, $options);
	}

	/**
	 * @param \SimpleXMLElement $structure
	 * @return array
	 */
	protected function decodeChildNames($structure) {
		$names = array();
		foreach ($structure->children() as $child) {
			$names[] = $child->getName();
		}
		return $names;
	}

	/**
	 * @param \SimpleXMLElement $nodeData
	 * @param EncoderNode $nodeProxy
	 * @param $isSingle
	 * @return mixed
	 */
	protected function decodeRawNode($nodeData, EncoderNode $nodeProxy, $isSingle) {
		$path = $nodeProxy->getNodeNameSingle();
		if (!$isSingle) {
			$path = $nodeProxy->getNodeName() . '/' . $path;
		}
		$children = $nodeData->xpath($path);
		return ($isSingle ? $children[0] : $children);
	}
	protected function decodeNode($nodeData) {
		$nodeData = (array) $nodeData;
		$attributes = array();
		if (isset($nodeData['@attributes'])) {
			$attributes = $nodeData['@attributes'];
		}
		unset($nodeData['@attributes']);
		return array_merge($attributes, $nodeData);
	}
} 