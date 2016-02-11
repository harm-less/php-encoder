<?php

namespace PE\Library;

class Inflector {

	/**
	 * Method cache array.
	 *
	 * @var array
	 */
	protected static $_cache = array();

	/**
	 * Cache inflected values, and return if already available
	 *
	 * @param string $type Inflection type
	 * @param string $key Original value
	 * @param bool|string $value Inflected value
	 * @return string Inflected value, from cache
	 */
	protected static function _cache($type, $key, $value = false) {
		$key = '_' . $key;
		$type = '_' . $type;
		if ($value !== false) {
			self::$_cache[$type][$key] = $value;
			return $value;
		}
		if (!isset(self::$_cache[$type][$key])) {
			return false;
		}
		return self::$_cache[$type][$key];
	}

	/**
	 * Returns the given lower_case_and_underscored_word as a CamelCased word.
	 *
	 * @param string $lowerCaseAndUnderscoredWord Word to camelize
	 * @param bool $lcfirst
	 * @param string $searchString
	 * @return string Camelized word. LikeThis.
	 */
	public static function camelize($lowerCaseAndUnderscoredWord, $lcfirst = true, $searchString = '_') {
		$cache = $lowerCaseAndUnderscoredWord . ($lcfirst ? '1' : '0') . $searchString;
		if (!($result = self::_cache(__FUNCTION__, $cache))) {
			$result = str_replace(' ', '', Inflector::humanize($lowerCaseAndUnderscoredWord, $searchString));
			if ($lcfirst) {
				$result = function_exists('lcfirst') ? lcfirst($result) : strtolower($result[0]).substr($result, 1);
			}
			self::_cache(__FUNCTION__, $cache, $result);
		}
		return $result;
	}


	/**
	 * Returns the given camelCasedWord as an underscored_word.
	 *
	 * @param string $camelCasedWord Camel-cased word to be "underscorized"
	 * @param string $replacement
	 * @return string Underscore-syntaxed version of the $camelCasedWord
	 */
	public static function underscore($camelCasedWord, $replacement = "_") {
		$cache = $camelCasedWord . $replacement;
		if (!($result = self::_cache(__FUNCTION__, $cache))) {
			$result = strtolower(preg_replace('/(?<=\\w)([A-Z])/', $replacement. '\\1', $camelCasedWord));
			self::_cache(__FUNCTION__, $cache, $result);
		}
		return $result;
	}

	/**
	 * Returns the given underscored_word_group as a Human Readable Word Group.
	 * (Underscores are replaced by spaces and capitalized following words.)
	 *
	 * @param string $lowerCaseAndUnderscoredWord String to be made more readable
	 * @param string $searchString
	 * @return string Human-readable string
	 */
	public static function humanize($lowerCaseAndUnderscoredWord, $searchString = '_') {
		$cache = $lowerCaseAndUnderscoredWord . $searchString;
		if (!($result = self::_cache(__FUNCTION__, $cache))) {
			$result = ucwords(str_replace($searchString, ' ', $lowerCaseAndUnderscoredWord));
			self::_cache(__FUNCTION__, $cache, $result);
		}
		return $result;
	}
} 