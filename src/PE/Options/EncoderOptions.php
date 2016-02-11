<?php

namespace PE\Options;

use PE\Exceptions\EncoderOptionsException;
use PE\Nodes\EncoderNode;

class EncoderOptions {

	/**
	 * @var array
	 */
	private $options;

	/**
	 * @var array
	 */
	private $cached;

	const ROOT = "%%root%%";

	const CHILDREN_ALL = "all_children";

	function __construct($options) {
		$this->options = (empty($options) ? array() : $options);
		$this->cached = array();
	}

	/**
	 * @return array Raw options array
	 */
	public function getRawOptions() {
		return $this->options;
	}

	/**
	 * @param array $options
	 * @param null|string $nodeName
	 */
	public function setOptions($options, $nodeName = null) {
		if ($nodeName === null) {
			$this->options = array_merge($this->options, $options);
			$this->resetCache();
		}
		else {
			$newOptions = (isset($this->options[$nodeName]) ? array_merge($this->options[$nodeName], $options) : $options);
			$this->options[$nodeName] = $newOptions;
			$this->resetCache($nodeName);
		}
	}


	/**
	 * @param string $optionName The name of the option
	 * @param null|string|EncoderNode $node Can be used to get an option from a specific node.
	 * @return string Should return the value of the requested option if found
	 */
	public function option($optionName, $node = null) {
		$options = null;
		if ($node === null || is_string($node)) {
			$options = $this->_processOptions($node);
		}
		else {
			$options = $this->processOptionsFromNode($node);
		}
		return $this->_option($options, $optionName);
	}
	/**
	 * @param string $optionName The name of the option
	 * @param null|string|EncoderNode $nodeName Can be used to check an option from a specific node
	 * @return bool
	 */
	public function hasOption($optionName, $nodeName = null) {
		return $this->option($optionName, $nodeName) !== null;
	}

	/**
	 * @param array $options Array with the option
	 * @param string $optionName Name of the option you want to grab
	 * @return null|mixed Returns the value of the option if it exists. Returns null if not found
	 */
	protected function _option($options, $optionName) {
		if (isset($options[$optionName])) {
			return $options[$optionName];
		}
		return null;
	}

	/**
	 * @return array Returns the root options (without a node)
	 */
	public function getRootOptions() {
		return $this->_processOptions(self::ROOT);
	}

	/**
	 * Check if a node exists. This also checks if the node exists as a node in EncoderNode::nodeExists()
	 *
	 * @param string $nodeName The node name you want to check
	 * @return bool Returns true if the node exists
	 *
	 * @see rawNodeExists()
	 * @see EncoderNode::nodeExists()
	 */
	public function nodeExists($nodeName) {
		return $this->rawNodeExists($nodeName) && EncoderNode::nodeExists($nodeName);
	}

	/**
	 * Simpler version in comparison to EncoderOptions::nodeExists(). It just checks if the node has any options
	 *
	 * @param string $nodeName The node name you want to check
	 * @return bool Returns true if the node exists in the options
	 */
	public function rawNodeExists($nodeName) {
		return isset($this->options[$nodeName]) && is_array($this->options[$nodeName]);
	}

	/**
	 * @param string $nodeName
	 * @return array Returns the raw node array
	 */
	public function getRawNode($nodeName) {
		if (!isset($this->options[$nodeName])) {
			throw new EncoderOptionsException(sprintf('Cannot get raw node "%s" because it doesn\'t exist', $nodeName));
		}
		return $this->options[$nodeName];
	}

	protected function _decodeNodeName($nodeName) {
		$node = array();

		$levels = explode(':', $nodeName);
		foreach ($levels as $level) {
			$levelArr = array();

			$match = '/\[([^"])\]+/';
			preg_match($match, $level, $id);
			if (count($id)) {
				$levelArr['nodeId'] = $id[1];
			}
			$levelArr['node'] = preg_replace($match, '', $level);

			$node[] = $levelArr;
		}
		return $node;
	}

	public function processOptionsFromNode(EncoderNode $node) {
		return array_merge($this->_processOptions($node->getNodeNameSingle()), $this->_processOptions($node->getNodeName()));
	}

	protected function _processOptions($nodeName = null) {

		$nodeName = ($nodeName === null ? self::ROOT : $nodeName);

		if (isset($this->cached[$nodeName])) {
			return $this->cached[$nodeName];
		}

		$decodedNode = $this->_decodeNodeName($nodeName);

		$lastChild = $decodedNode[count($decodedNode) - 1];
		$lastChildNode = $lastChild['node'];


		$optionPaths = (array(self::ROOT, $lastChildNode, $nodeName));

		$mergedOptions = array();
		foreach ($optionPaths as $optionPath) {

			$pathOptions = array();

			if (isset($this->cached[$optionPath])) {
				$mergedOptions = array_merge($mergedOptions, $this->cached[$optionPath]);
				continue;
			}
			else {
				if ($optionPath == self::ROOT) {
					$rootOptions = array();
					foreach ($this->getRawOptions() as $key => $value) {
						if (!is_array($value)) {
							$rootOptions[$key] = $value;
						}
					}
					$pathOptions = $rootOptions;
				} else if ($this->rawNodeExists($optionPath)) {
					$pathOptions = $this->getRawNode($optionPath);
				}

				$mergedOptions = array_merge($mergedOptions, $pathOptions);

				$this->cached[$optionPath] = $mergedOptions;
			}
		}

		return $mergedOptions;
	}

	protected function resetCache($nodeName = null) {
		if ($nodeName === null) {
			unset($this->cached[self::ROOT]);
		}
		else {
			$this->cached = array();
		}
	}
}