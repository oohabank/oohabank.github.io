<?php

namespace App\Models\Users;

use App\Models\Log;
use Component\Database\Database;

class User {
	private $id, $email, $login, $date_create, $date_update;

	private $password = '';

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

		if(isset($data['email'])){
			$this->setEmail($data['email']);
		}

		if(isset($data['login'])){
			$this->setLogin($data['login']);
		}

		if(isset($data['password'])){
			$this->setPassword($data['password']);
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

	public function getEmail() : ?string {
		return $this->email;
	}

	public function setEmail(?string $email) : self {
		$this->email = $email;

		return $this;
	}

	public function getLogin() : ?string {
		return $this->login;
	}

	public function setLogin(?string $login) : self {
		$this->login = $login;

		return $this;
	}

	public function getPassword() : string {
		return $this->password;
	}

	public function setPassword(string $password) : self {
		$this->password = $password;

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

		$and = "";

		$prepare = [];

		if(!is_null($this->getID())){
			$and .= " AND `id` = '?'";
			$prepare[] = $this->getID();
		}

		if(!is_null($this->getEmail())){
			$email = addcslashes(Database::mysql()->escape_string($this->getEmail()), '_%$[]');
			$and .= " AND `email` LIKE '{$email}'";
		}

		if(!is_null($this->getLogin())){
			$login = addcslashes(Database::mysql()->escape_string($this->getLogin()), '_%$[]');
			$and .= " AND `login` LIKE '{$login}'";
		}

		$query = Database::prepare("SELECT * FROM `users` WHERE 1 {$and} LIMIT 1", $prepare);

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

		if(is_null($this->getLogin())){
			$this->setLogin('');
		}

		if(is_null($this->getEmail())){
			$this->setEmail('');
		}

		$insert = Database::prepare("INSERT INTO `users`
												(`login`, `email`, `password`, `date_create`, `date_update`)
											VALUES
												('?', '?', '?', '?', '?')", [
													$this->getLogin(), $this->getEmail(), $this->getPassword(), $this->getDateCreate(), $this->getDateUpdate()
		]);

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

		$values = [
			$this->getEmail(),
			$this->getLogin(),
			$this->getPassword(),
			$this->getDateUpdate(),
			$this->getID()
		];

		$update = Database::prepare("UPDATE `users`
									SET 
										`email` = '?',
										`login` = '?',
										`password` = '?',
										`date_update` = '?'
									WHERE `id` = '?'
									LIMIT 1", $values);

		if(!$update){
			Log::error('SQL Error in '.__FILE__.' on line '.__LINE__.' ('.Database::mysql()->error.')');

			return false;
		}

		return true;
	}

	public function delete(?int $id = null) : bool {
		if(!is_null($id)){ $this->setID(intval($id)); }

		$delete = Database::prepare("DELETE FROM `users` WHERE `id` = '?' LIMIT 1", [$this->getID()]);

		if(!$delete){
			Log::error('SQL Error in '.__FILE__.' on line '.__LINE__.' ('.Database::mysql()->error.')');

			return false;
		}

		return true;
	}
}

?>