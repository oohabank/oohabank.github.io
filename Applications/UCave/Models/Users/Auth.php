<?php

namespace App\Models\Users;

use App\Models\Log;
use Component\Database\Database;

class Auth {
	private $id, $user_id, $ip, $token, $date_create, $date_expire;

	public function __construct(?array $import = null){
		if(!is_null($import)){
			$this->import($import);
		}
	}

	public function import(array $data) : self {
		if(empty($data)){ return $this; }

		if(isset($data['id'])){
			$this->setID(intval($data['id']));
		}

		if(isset($data['user_id'])){
			$this->setUserID(intval($data['user_id']));
		}

		if(isset($data['ip'])){
			$this->setIP($data['ip']);
		}

		if(isset($data['token'])){
			$this->setToken($data['token']);
		}

		if(isset($data['date_create'])){
			$this->setDateCreate(intval($data['date_create']));
		}

		if(isset($data['date_expire'])){
			$this->setDateExpire(intval($data['date_expire']));
		}

		return $this;
	}

	public function getID() : ?int {
		return $this->id;
	}

	public function setID(int $id) : self {
		$this->id = $id;

		return $this;
	}

	public function getUserID() : ?int {
		return $this->user_id;
	}

	public function setUserID(?int $id) : self {
		$this->user_id = $id;

		return $this;
	}

	public function getToken() : string {
		return $this->token;
	}

	public function setToken(string $token) : self {
		$this->token = $token;

		return $this;
	}

	public function getIP() : ?string {
		return $this->ip;
	}

	public function setIP(?string $ip) : self {
		$this->ip = $ip;

		return $this;
	}

	public function getDateCreate() : int {
		if(is_null($this->date_create)){
			$this->setDateCreate(time());
		}

		return $this->date_create;
	}

	public function setDateCreate(?int $unixtime) : self {
		$this->date_create = $unixtime;

		return $this;
	}

	public function getDateExpire() : int {
		if(is_null($this->date_expire)){
			$this->setDateExpire(time());
		}

		return $this->date_expire;
	}

	public function setDateExpire(?int $unixtime) : self {
		$this->date_expire = $unixtime;

		return $this;
	}

	public function get(?int $id = null) : bool {
		if(!is_null($id)){
			$this->setID($id);
		}

		$and = "";

		if(!is_null($this->getID())){
			$and .= " AND `id` = '{$this->getID()}'";
		}

		if(!is_null($this->getUserID())){
			$and .= " AND `user_id` = '{$this->getUserID()}'";
		}

		if(!is_null($this->getToken())){
			$token = addcslashes(Database::mysql()->escape_string($this->getToken()), '_%$[]');
			$and .= " AND `token` LIKE '{$token}'";
		}

		if(!is_null($this->getIP())){
			$ip = addcslashes(Database::mysql()->escape_string($this->getIP()), '_%$[]');
			$and .= " AND `ip` LIKE '{$ip}'";
		}

		$query = Database::mysql()->query("SELECT * FROM `users_auth` WHERE 1 {$and} LIMIT 1");

		if(!$query){
			Log::error('SQL Error in '.__FILE__.' on line '.__LINE__.' ('.Database::mysql()->error.')');

			return false;
		}

		if(!$query->num_rows){
			return false;
		}

		$this->import($query->fetch_assoc());

		return true;
	}

	public function insert() : bool {

		$insert = Database::prepare("INSERT INTO `users_auth`
												(`user_id`, `token`, `ip`, `date_create`, `date_expire`)
											VALUES
												('?', '?', '?', '?', '?')", [$this->getUserID(), $this->getToken(), $this->getIP(), $this->getDateCreate(), $this->getDateExpire()]);

		if(!$insert){
			Log::error('SQL Error in '.__FILE__.' on line '.__LINE__.' ('.Database::mysql()->error.')');

			return false;
		}

		$this->setID(Database::mysql()->insert_id);

		return true;
	}

	public function update(?int $id = null) : bool {
		if(!is_null($id)){ $this->setID(intval($id)); }

		$update = Database::prepare("UPDATE `users_auth`
									SET `user_id` = '?', `ip` = '?', `token` = '?', `date_expire` = '?'
									WHERE `id` = '?'
									LIMIT 1", [
			$this->getUserID(),
			$this->getIP(),
			$this->getToken(),
			$this->getDateExpire(),
			$this->getID()
		]);

		if(!$update){
			Log::error('SQL Error in '.__FILE__.' on line '.__LINE__.' ('.Database::mysql()->error.')');

			return false;
		}

		return true;
	}

	public function delete(?int $id = null) : bool {
		if(!is_null($id)){ $this->setID(intval($id)); }

		$delete = Database::prepare("DELETE FROM `users_auth` WHERE `id` = '?' LIMIT 1", [$this->getID()]);

		if(!$delete){
			Log::error('SQL Error in '.__FILE__.' on line '.__LINE__.' ('.Database::mysql()->error.')');

			return false;
		}

		return true;
	}

	public function save() : bool {
		$_SESSION[Users::SESSION_NAME] = $this->getToken();

		setcookie(Users::SESSION_NAME, $this->getToken(), $this->getDateExpire(), '/', 'gta5rating.com');

		return true;
	}

	public function unsave() : bool {
		if(isset($_SESSION[Users::SESSION_NAME])){
			unset($_SESSION[Users::SESSION_NAME]);
		}

		setcookie(Users::SESSION_NAME, '', time() - 86400, '/', 'gta5rating.com');

		return true;
	}
}

?>