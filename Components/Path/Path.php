<?php

namespace Component\Path;

use Component\Cache\Cache;

class Path {

	public static function getRoot() : string {
		$cache = Cache::get('__ROOT__DEFINE__');
		if(!is_null($cache)){ return $cache; }

		return Cache::set('__ROOT__DEFINE__', dirname(dirname(__DIR__)));
	}

	public static function to(string $path) : string {
		return self::getRoot()."/Applications/UCave{$path}";
	}
}

?>