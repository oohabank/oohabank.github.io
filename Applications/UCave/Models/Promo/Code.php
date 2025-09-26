<?php

namespace App\Models\Promo;

use App\Models\Log;
use App\Models\Users\Users;
use Component\Database\Database;

class Code {
	private $id, $code, $discount, $user_id_create, $user_id_update, $date_create, $date_update;

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

		if(isset($data['code'])){
			$this->setCode($data['code']);
		}

		if(isset($data['discount'])){
			$this->setDiscount(floatval($data['discount']));
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

	public function getCode() : ?string {
		return $this->code;
	}

	public function setCode(?string $code) : self {
		$this->code = $code;

		return $this;
	}

	public function getDiscount() : ?float {
		return $this->discount;
	}

	public function setDiscount(?float $discount) : self {
		$this->discount = $discount;

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

		$prepare = [];
		$and = "";

		if(!is_null($this->getID())){
			$and .= " AND `id` = '?'";
			$prepare[] = $this->getID();
		}

		if(!is_null($this->getCode())){
			$and .= " AND `code` = '?'";
			$prepare[] = $this->getCode();
		}

		if(!is_null($this->getDiscount())){
			$and .= " AND `discount` = '?'";
			$prepare[] = $this->getDiscount();
		}

		$query = Database::prepare("SELECT * FROM `promo` WHERE 1 {$and} LIMIT 1", $prepare);


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
		if(is_null($this->getCode())){
			$this->setCode(md5(mt_rand(99999, 99999999)));
		}

		if(is_null($this->getDiscount())){
			$this->setDiscount(0);
		}

		$insert = Database::prepare("INSERT INTO `promo`
										(`code`, `discount`, `user_id_create`, `user_id_update`, `date_create`, `date_update`)
									VALUES
										('?', '?', '?', '?', '?', '?')", [
			$this->getCode(), $this->getDiscount(), $this->getCreatorID(),
			$this->getUpdaterID(), $this->getDateCreate(), $this->getDateUpdate()
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

		$update = Database::prepare("UPDATE `promo`
									SET `code` = '?', `discount` = '?',
										`user_id_update` = '?', `date_update` = '?'
									WHERE `id` = '?'
									LIMIT 1", [
			$this->getCode(), $this->getDiscount(),
			$this->getUpdaterID(), $this->getDateUpdate(), $this->getID()
		]);

		if(!$update){
			Log::error('SQL Error in '.__FILE__.' on line '.__LINE__.' ('.Database::mysql()->error.')');

			return false;
		}

		return true;
	}

	public function delete(?int $id = null) : bool {
		if(!is_null($id)){ $this->setID(intval($id)); }

		$delete = Database::prepare("DELETE FROM `promo` WHERE `id` = '?' LIMIT 1", [$this->getID()]);

		if(!$delete){
			Log::error('SQL Error in '.__FILE__.' on line '.__LINE__.' ('.Database::mysql()->error.')');

			return false;
		}

		return true;
	}
}

?>