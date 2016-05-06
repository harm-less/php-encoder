<?php

namespace PE\Nodes\Specials;

use PE\Enums\ActionVariable;
use PE\Nodes\EncoderNode;
use PE\Nodes\EncoderNodeVariable;
use PE\Variables\Types\NodeAccessor;
use PE\Variables\Types\PostNodeGetter;
use PE\Variables\Types\PostNodeSetter;

class AccessorMethodActionTypeNodeNode extends EncoderNode {

	function __construct() {
		parent::__construct('accessor-method-action-type-nodes', 'accessor-method-action-type-node', '\\PE\\Samples\\Specials');

		$this->addVariable(new EncoderNodeVariable('special'));

		$special = $this->addVariable(new EncoderNodeVariable('node'));
		$special->postNodeSetter(new PostNodeSetter('addNodeToSpecial', array(NodeAccessor::VARIABLE_NAME)));
		$special->postNodeGetter(new PostNodeGetter('getNodeFromSpecial', array(NodeAccessor::VARIABLE_NAME)));
	}

	public function addNodeToSpecial($data, $setterName) {
		$data['special'] = $data[$setterName];
		return $data;
	}

	public function getNodeFromSpecial($data, $getterName) {
		$data['special'] = $data[$getterName] . ' getter';
		return $data;
	}
}