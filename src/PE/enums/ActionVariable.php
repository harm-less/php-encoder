<?php

namespace PE\Enums;

use PE\Nodes\EncoderNodeVariable;

class ActionVariable {

	const SETTER_NODE_DATA = EncoderNodeVariable::ACTION_VARIABLE_SETTER_NODE_DATA;
	const SETTER_NAME = 'name';
	const SETTER_VALUE = 'value';
    const SETTER_PARENT = 'parent';

	const GETTER_NODE_DATA = EncoderNodeVariable::ACTION_VARIABLE_GETTER_NODE_DATA;
	const GETTER_NAME = 'name';
	const GETTER_VALUE = 'value';
    const GETTER_OBJECT = 'object';
    const GETTER_PARENT = 'parent';
	const GETTER_OPTIONS = 'options';
    const GETTER_NODE_ITERATION_INDEX = 'node_iteration';
    const GETTER_CHILD_OBJECT_ITERATION_INDEX = 'child_object_iteration';
} 