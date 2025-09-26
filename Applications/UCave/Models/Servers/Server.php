<?php

namespace App\Models\Servers;

use App\Models\Log;
use App\Models\Users\Users;
use Component\Database\Database;

class Server {
	private $id, $rcon_host, $rcon_port, $rcon_password, $user_id_create, $user_id_update, $date_create, $date_update;

	private $title = '';

	private $text = '';

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

		if(isset($data['title'])){
			$this->setTitle($data['title']);
		}

		if(isset($data['text'])){
			$this->setText($data['text']);
		}

		if(isset($data['rcon_host'])){
			$this->setRconHost($data['rcon_host']);
		}

		if(isset($data['rcon_port'])){
			$this->setRconPort(intval($data['rcon_port']));
		}

		if(isset($data['rcon_password'])){
			$this->setRconPassword($data['rcon_password']);
		}

		if(isset($data['date_create'])){
			$this->setDateCreate(intval($data['date_create']));
		}

		if(isset($data['date_update'])){
			$this->setDateUpdate(intval($data['date_update']));
		}

		if(isset($data['user_id_create'])){
			$this->setCreatorID(intval($data['user_id_create']));
		}

		if(isset($data['user_id_update'])){
			$this->setUpdaterID(intval($data['user_id_update']));
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

	public function getTitle() : string {
		return $this->title;
	}

	public function setTitle(string $title) : self {
		$this->title = $title;

		return $this;
	}

	public function getText() : string {
		return $this->text;
	}

	public function setText(string $text) : self {
		$this->text = $text;

		return $this;
	}

	public function getRconHost() : ?string {
		return $this->rcon_host;
	}

	public function setRconHost(?string $host) : self {
		$this->rcon_host = $host;

		return $this;
	}

	public function getRconPort() : ?int {
		return $this->rcon_port;
	}

	public function setRconPort(?int $port) : self {
		$this->rcon_port = $port;

		return $this;
	}

	public function getRconPassword() : ?string {
		return $this->rcon_password;
	}

	public function setRconPassword(?string $password) : self {
		$this->rcon_password = $password;

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

	public function getCreatorID() : int {
		if(is_null($this->user_id_create)){
			$current = Users::current();
			$this->setCreatorID(is_null($current) ? 0 : $current->getID());
		}

		return $this->user_id_create;
	}

	public function setCreatorID(?int $id) : self {
		$this->user_id_create = $id;

		return $this;
	}

	public function getUpdaterID() : int {
		if(is_null($this->user_id_update)){
			$current = Users::current();
			$this->setUpdaterID(is_null($current) ? 0 : $current->getID());
		}

		return $this->user_id_update;
	}

	public function setUpdaterID(?int $id) : self {
		$this->user_id_update = $id;

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

		if(!is_null($this->getRconHost())){
			$and .= " AND `rcon_host` = '?'";
			$prepare[] = $this->getRconHost();
		}

		if(!is_null($this->getRconPort())){
			$and .= " AND `rcon_port` = '?'";
			$prepare[] = $this->getRconPort();
		}

		if(!is_null($this->getRconPassword())){
			$and .= " AND `rcon_password` = '?'";
			$prepare[] = $this->getRconPassword();
		}

		$query = Database::prepare("SELECT *
									FROM `servers`
									WHERE 1 {$and}
									LIMIT 1", $prepare);


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

		if(is_null($this->getRconHost())){
			$this->setRconHost('127.0.0.1');
		}

		if(is_null($this->getRconPassword())){
			$this->setRconPassword('');
		}

		if(is_null($this->getRconPort())){
			$this->setRconPort(25575);
		}

		$insert = Database::prepare("INSERT INTO `servers`
										(`title`, `text`, `rcon_host`, `rcon_port`,
										`rcon_password`, `date_create`, `date_update`,
										`user_id_create`, `user_id_update`)
									VALUES
										('?', '?', '?', '?', '?', '?', '?', '?', '?')", [
										$this->getTitle(), $this->getText(), $this->getRconHost(),
										$this->getRconPort(), $this->getRconPassword(),
										$this->getDateCreate(), $this->getDateUpdate(),
										$this->getCreatorID(), $this->getUpdaterID()
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

		$this->setDateUpdate(null)->setUpdaterID(null);

		$update = Database::prepare("UPDATE `servers`
									SET `title` = '?', `text` = '?', `rcon_host` = '?', `rcon_port` = '?',
										`rcon_password` = '?',  `date_update` = '?', `user_id_update` = '?'
									WHERE `id` = '?'
									LIMIT 1", [
			$this->getTitle(), $this->getText(), $this->getRconHost(), $this->getRconPort(),
			$this->getRconPassword(), $this->getDateUpdate(), $this->getUpdaterID(), $this->getID()
		]);

		if(!$update){
			Log::error('SQL Error in '.__FILE__.' on line '.__LINE__.' ('.Database::mysql()->error.')');

			return false;
		}

		return true;
	}

	public function delete(?int $id = null) : bool {
		if(!is_null($id)){ $this->setID(intval($id)); }

		$delete = Database::prepare("DELETE FROM `servers` WHERE `id` = '?' LIMIT 1", [$this->getID()]);

		if(!$delete){
			Log::error('SQL Error in '.__FILE__.' on line '.__LINE__.' ('.Database::mysql()->error.')');

			return false;
		}

		return true;
	}
}

?>