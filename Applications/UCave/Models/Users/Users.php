<?php

namespace App\Models\Users;

use App\Bootstrap;
use App\Models\Log;
use Component\Cache\Cache;
use Component\Database\Database;

class Users {

	const PAGINATION_LIMIT = 20;

	const SESSION_NAME = 'auth';

	public static function User(?array $import = null) : User {
		return new User($import);
	}

	public static function Listing() : Listing {
		return new Listing();
	}

	public static function Auth(?array $import = null) : Auth {
		return new Auth($import);
	}

	public static function IP() : string {
		if(!empty($_SERVER['HTTP_CF_CONNECTING_IP'])){
			$ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
		}elseif(!empty($_SERVER['HTTP_X_REAL_IP'])){
			$ip = $_SERVER['HTTP_X_REAL_IP'];
		}elseif(!empty($_SERVER['HTTP_CLIENT_IP'])){
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		}elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}else{
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		preg_match("/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/i", $ip, $matches);

		return (isset($matches[0])) ? $matches[0] : mb_substr($ip, 0, 16, "UTF-8");
	}

	public static function currentAuth() : ?Auth {
		$cache = Cache::get(__METHOD__);
		if(!is_null($cache)){ return $cache; }

		$auth = self::Auth();

		if(isset($_SESSION[self::SESSION_NAME]) && !empty($_SESSION[self::SESSION_NAME])){
			$auth->setToken($_SESSION[self::SESSION_NAME]);
		}elseif(isset($_COOKIE[self::SESSION_NAME]) && !empty($_COOKIE[self::SESSION_NAME])){
			$auth->setToken($_COOKIE[self::SESSION_NAME]);
		}else{
			return null;
		}

		if(!$auth->get()){ return null; }

		if($auth->getDateExpire() < time()){ return null; }

		Cache::set(__METHOD__, $auth);

		return $auth;
	}

	public static function current() : ?User {
		$cache = Cache::get(__METHOD__);
		if(!is_null($cache)){ return $cache; }

		$auth = self::currentAuth();

		if(is_null($auth)){ return null; }

		$user = self::User()->setID($auth->getUserID());

		if(!$user->get()){ return null; }

		Cache::set(__METHOD__, $user);

		return $user;
	}

	public static function createPassword(string $password = '') : string {
		return password_hash($password, PASSWORD_BCRYPT, [
			'cost' => 12
		]);
	}

	public static function checkPassword(string $password, string $hash) : bool {
		return password_verify($password, $hash);
	}

	public static function exists(string $login) : bool {
		$config = Bootstrap::getConfig();

		$query = Database::prepare("SELECT COUNT(1) FROM `vk_auth` WHERE `{$config['database']['users']}` = '?' LIMIT 1", [$login]);

		if(!$query){
			Log::error('SQL Error in '.__FILE__.' on line '.__LINE__.' ('.Database::mysql()->error.')');

			return false;
		}

		$ar = $query->fetch_array();

		return intval($ar[0]) > 0;
	}
}

?>