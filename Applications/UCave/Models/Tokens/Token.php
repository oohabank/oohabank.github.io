<?php

namespace App\Models\Tokens;

use App\Models\Log;
use App\Models\Users\Users;
use Component\Database\Database;

class Token {
	private $id, $user_id, $token, $ip, $type, $date_create, $date_update;

	private $data = '';

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

		if(isset($data['token'])){
			$this->setToken($data['token']);
		}

		if(isset($data['ip'])){
			$this->setIP($data['ip']);
		}

		if(isset($data['type'])){
			$this->setType($data['type']);
		}

		if(isset($data['data'])){
			$this->setData($data['data']);
		}

		if(isset($data['date_create'])){
			$this->setDateCreate(intval($data['date_create']));
		}

		if(isset($data['date_update'])){
			$this->setDateUpdate(intval($data['date_update']));
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

	public function getToken() : ?string {
		return $this->token;
	}

	public function setToken(?string $token) : self {
		$this->token = $token;

		return $this;
	}

	public function getType() : ?string {
		return $this->type;
	}

	public function setType(?string $type) : self {
		$this->type = $type;

		return $this;
	}

	public function getData() : ?string {
		return $this->data;
	}

	public function setData(?string $data) : self {
		$this->data = $data;

		return $this;
	}

	public function getIP() : string {
		if(is_null($this->ip)){
			$this->setIP(Users::IP());
		}

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

	public function getDateUpdate() : int {
		if(is_null($this->date_update)){
			$this->setDateUpdate(time());
		}

		return $this->date_update;
	}

	public function setDateUpdate(?int $unixtime) : self {
		$this->date_update = $unixtime;

		return $this;
	}

	public function get(?int $id = null) : bool {
		if(!is_null($id)){
			$this->setID($id);
		}

		$where = "";

		$prepare = [];

		if(!is_null($this->getID())){
			$where .= " AND `id` = '?'";
			$prepare[] = $this->getID();
		}

		if(!is_null($this->getUserID())){
			$where .= " AND `user_id` = '?'";
			$prepare[] = $this->getUserID();
		}

		if(!is_null($this->getIP())){
			$where .= " AND `ip` = '?'";
			$prepare[] = $this->getIP();
		}

		if(!is_null($this->getToken())){
			$where .= " AND `token` = '?'";
			$prepare[] = $this->getToken();
		}

		if(!is_null($this->getType())){
			$where .= " AND `type`='?'";
			$prepare[] = $this->getType();
		}

		$query = Database::prepare("SELECT * FROM `tokens` WHERE 1 {$where} LIMIT 1", $prepare);

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


		$insert = Database::prepare("INSERT INTO `tokens`
												(`user_id`, `token`, `ip`, `type`, `data`, `date_create`, `date_update`)
											VALUES
												('?', '?', '?', '?', '?', '?', '?')", [$this->getUserID(), $this->getToken(), $this->getIP(), $this->getType(), $this->getData(), $this->getDateCreate(), $this->getDateUpdate()]);

		if(!$insert){
			Log::error('SQL Error in '.__FILE__.' on line '.__LINE__.' ('.Database::mysql()->error.')');

			return false;
		}

		$this->setID(Database::mysql()->insert_id);

		return true;
	}

	public function update(?int $id = null) : bool {
		if(!is_null($id)){ $this->setID(intval($id)); }

		$this->setDateUpdate(null);

		$update = Database::prepare("UPDATE `tokens`
									SET `user_id`='?', `token`='?', `ip`='?', `data`='?', `type`='?', `date_update`='?'
									WHERE `id`='?'
									LIMIT 1", [$this->getUserID(), $this->getToken(), $this->getIP(), $this->getData(), $this->getType(), $this->getDateUpdate(), $this->getID()]);

		if(!$update){
			Log::error('SQL Error in '.__FILE__.' on line '.__LINE__.' ('.Database::mysql()->error.')');

			return false;
		}

		return true;
	}

	public function delete(?int $id = null) : bool {
		if(!is_null($id)){ $this->setID(intval($id)); }

		$delete = Database::prepare("DELETE FROM `tokens` WHERE `id`='?' LIMIT 1", [$this->getID()]);

		if(!$delete){
			Log::error('SQL Error in '.__FILE__.' on line '.__LINE__.' ('.Database::mysql()->error.')');

			return false;
		}

		return true;
	}
}

?>