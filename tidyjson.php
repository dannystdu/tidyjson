<?php

class TidyJSON {
	protected static $default_config = array(
		'indent'		=>	'  ',
		'space'			=>	' ',
	);

	protected static $string_chars = array('"', "'");
	protected static $white_chars = array(" ", "\t", "\n", "\r");

	public static function tidy($json, $config = null) {
		$config = self::get_config($config);
		$out = '';
		$level = 0;
		$strchr = null;
		for ($x = 0; $x < strlen($json); $x++) {
			$c = substr($json, $x, 1);
			if ($strchr === null) {
				if (in_array($c, self::$white_chars))
					continue;
				if (in_array($c, self::$string_chars)) {
					$strchr = $c;
				} else {
					if ($c == '{' || $c == '[') {
						$eol = true;
						$level++;
					} elseif ($c == '}' || $c == ']') {
						$level--;
						$out .= "\n" . self::indent($level, $config);
					} elseif ($c == ',') {
						$eol = true;
					} elseif ($c == ':') {
						$c .= $config['space'];
					}
				}
			} else {
				if ($c === $strchr) {
					$strchr = null;
				}
			}
			$out .= $c;
			if ($eol) {
				$eol = false;
				$out .= "\n" . self::indent($level, $config);
			}
		}

		// Remove trailing whitespace
		while (in_array(substr($out, -1), self::$white_chars)) {
			$out = substr($out, 0, -1);
		}

		return $out;
	}

	protected static function indent($level, $config) {
		$out = '';
		for ($x = 0; $x < $level; $x++) $out .= $config['indent'];
		return $out;
	}

	protected static function get_config($config = null) {
		return is_array($config) ? array_merge(self::$default_config, $config) : self::$default_config;
	}
}

