<?php

namespace Component\Database;

use App\Bootstrap;
use mysqli;

class Database {
	/** @var ?mysqli */
	private static $connection;

	public static function mysql() : ?mysqli {
		return self::connection();
	}

	public static function alias(array $columns, string $prefix) : array {
		$result = [];

		foreach($columns as $column){
			$result[] = "`{$prefix}`.`{$column}` AS `{$prefix}_{$column}`";
		}

		return $result;
	}

	public static function unalias(array $columns, array $ar) : array {
		$result = [];

		foreach($columns as $as){
			$split = explode(' AS ', $as);

			$alias = str_replace('`', '', $split[1]);
			$split = explode('_', $alias);

			$prefix = $split[0];

			unset($split[0]);

			$column = implode('_', $split);

			$result[$column] = @$ar["{$prefix}_{$column}"];
		}

		return $result;
	}

	public static function prepare(string $query, array $values = []) {

		if(empty($values)){ return self::mysql()->query($query); }

		$split = str_split($query);

		$string = "";

		$i = 0;

		foreach($split as $char){
			if($char == '?' && isset($values[$i])){
				$string .= self::mysql()->escape_string(strval($values[$i]));
				$i++;
			}else{
				$string .= $char;
			}
		}

		return self::mysql()->query($string);
	}

	public static function connection() : ?mysqli {
		if(!is_null(self::$connection)){ return self::$connection; }

		$config = Bootstrap::getConfig();

		self::$connection = @self::connect($config['database']['host'], $config['database']['user'], $config['database']['password'], $config['database']['database'], $config['database']['port']);

		if(is_null(self::$connection)){
			exit('Error connection to database');
		}

		return self::$connection;
	}

	public static function connect(?string $hostname = '127.0.0.1', ?string $username = 'root', ?string $password = '', ?string $database = 'database', ?int $port = 3306) : ?mysqli {

		$connection = @new mysqli($hostname, $username, $password, $database, $port);

		if($connection->connect_errno){ $connection->close(); exit("Database connection error"); }

		if(!$connection->set_charset("utf8")){ $connection->close(); exit("Error set charset DB"); }

		return $connection;
	}
}

?>