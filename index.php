<?php

error_reporting( E_ALL );
ini_set("display_errors", 'On' );

require('tests/bootstrap.php');

use PE\Encoder;

$encoder = new Encoder();
//pr($encoder);