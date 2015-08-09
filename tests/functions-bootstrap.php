<?php

function pr($var) {
	$template = php_sapi_name() !== 'cli' ? '<pre>%s</pre>' : "\n%s\n";
	printf($template, str_replace(' ', '&nbsp;', print_r($var, true)));
}