<?php

//todo Cache variables

include 'functions-bootstrap.php';

define('TESTS_DIR', __DIR__);
define('ROOT_DIR', dirname(TESTS_DIR));

// Load our autoloader, and add our Test class namespace
$autoloader = require(ROOT_DIR . '/vendor/autoload.php');