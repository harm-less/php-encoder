<?php

namespace PE\Encoders;

use PE\Encoder;
use PE\Options\EncoderOptions;

class JsonEncoder extends Encoder {

	public function encode($object, EncoderOptions $options = null) {

		$arr = parent::encode($object, $options);
		$processed = isset($arr['processed']) ? $arr['processed'] : null;
		return $processed ? json_encode($processed) : null;
	}

	public function decode($json, EncoderOptions $options = null) {

		if (is_string($json)) {
            $json = json_decode($json);
		}
		return parent::decode($json, $options);
	}
} 