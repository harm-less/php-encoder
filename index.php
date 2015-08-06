<?php

error_reporting( E_ALL );
ini_set("display_errors", 'On' );

require('tests/bootstrap.php');

use PE\Encoder;

$encoder = new Encoder();
pr($encoder);

function pr($var) {
	$template = php_sapi_name() !== 'cli' ? '<pre>%s</pre>' : "\n%s\n";
	printf($template, str_replace(' ', '&nbsp;', print_r($var, true)));
}