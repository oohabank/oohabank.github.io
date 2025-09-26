<?php

namespace Component\Cache;

class Cache {
	private static $di = [];

	public static function token($data){
		return md5(var_export($data, true));
	}

	public static function set($key, $value){

		$token = self::token($key);

		self::$di[$token] = $value;

		return $value;
	}

	public static function has($key) : bool {

		$token = self::token($key);

		return isset(self::$di[$token]);
	}

	public static function get($key) {
		$token = self::token($key);

		if(!isset(self::$di[$token])){ return null; }

		return self::$di[$token];
	}
}

?>