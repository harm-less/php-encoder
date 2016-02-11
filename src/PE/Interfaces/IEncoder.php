<?php

namespace PE\Interfaces;

use PE\Options\EncoderOptions;

interface IEncoder
{
	public function encode($template, EncoderOptions $options = null);
	public function decode($template, EncoderOptions $options = null);
}