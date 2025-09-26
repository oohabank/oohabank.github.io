<?php

namespace App\Models;

class Log {
	public static function file(string $filename, string $message){
		$logdir = dirname($filename);

		if(!is_dir($logdir)){ @mkdir($logdir, 0755, true); }

		$date = date('h:m:s d.m.Y');

		$message = "{$date} > {$message}";

		if(!is_file($filename)){
			file_put_contents($filename, $message);
		}else{
			file_put_contents($filename, "\n{$message}", FILE_APPEND);
		}

		return $message;
	}

	public static function error(string $message){
		return self::file(__ROOT__."/tmp/logs/error.txt", $message);
	}

	public static function warn(string $message){
		return self::file(__ROOT__."/tmp/logs/warn.txt", $message);
	}
}

?>