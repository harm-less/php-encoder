<?php

namespace PE\Options;

use PE\Nodes\EncoderNode;

class EncoderOptions {

	private $options;

	private $cached;

    const ROOT = "%%root%%";

    const CHILDREN_ALL = "all_children";

	function __construct($options) {
		$this->options = (empty($options) ? array() : $options);
		$this->cached = array();
	}

	protected function resetCache($nodeName = null) {
		if ($nodeName === null) {
			unset($this->cached[self::ROOT]);
		}
		else {
			$this->cached = array();
		}
	}

    public function getRawOptions() {
        return $this->options;
    }
    public function getRootOptions() {
        return $this->_processOptions(self::ROOT);
    }

    public function nodeExists($nodeName) {
        return $this->rawNodeExists($nodeName) && EncoderNode::nodeExists($nodeName);
    }
    public function rawNodeExists($nodeName) {
        return isset($this->options[$nodeName]) && is_array($this->options[$nodeName]);
    }
    public function getRawNode($nodeName) {
        return $this->options[$nodeName];
    }

	public function hasOption($optionName, $nodeName = null) {
		return $this->option($optionName, $nodeName) !== null;
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

	protected function _option($options, $optionName) {
		if (isset($options[$optionName])) {
			return $options[$optionName];
		}
		return null;
    }

	public function processOptionsFromNode(EncoderNode $node) {
		return array_merge($this->_processOptions($node->getNodeName(), $this->_processOptions($node->getNodeNameSingle())));
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
} 