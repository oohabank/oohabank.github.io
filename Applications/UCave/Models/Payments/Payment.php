<?php

namespace App\Models\Payments;

use App\Models\Categories\Category;
use App\Models\Items\Item;
use App\Models\Log;
use App\Models\Sections\Section;
use App\Models\Servers\Server;
use Component\Database\Database;

class Payment {
	private $id, $item_id, $sum, $status, $done, $player, $date_create, $date_update, $item, $server, $category, $section, $promo_id;

	private $data = '';

	private $amount = 1;

	private $response = '';

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

		if(isset($data['item_id'])){
			$this->setItemID(intval($data['item_id']));
		}

		if(isset($data['sum'])){
			$this->setSum(floatval($data['sum']));
		}

		if(isset($data['status'])){
			$this->setStatus(intval($data['status']));
		}

		if(isset($data['done'])){
			$this->setDone(intval($data['done']));
		}

		if(isset($data['promo_id'])){
			$this->setPromoID(intval($data['promo_id']));
		}

		if(isset($data['player'])){
			$this->setPlayer($data['player']);
		}

		if(isset($data['amount'])){
			$this->setAmount(intval($data['amount']));
		}

		if(isset($data['response'])){
			$this->setResponse($data['response']);
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

	public function getItemID() : ?int {
		return $this->item_id;
	}

	public function setItemID(?int $id) : self {
		$this->item_id = $id;

		return $this;
	}

	public function getPromoID() : ?int {
		return $this->promo_id;
	}

	public function setPromoID(?int $id) : self {
		$this->promo_id = $id;

		return $this;
	}

	public function getSum() : ?float {
		return $this->sum;
	}

	public function setSum(?float $sum) : self {
		$this->sum = $sum;

		return $this;
	}

	public function getStatus() : ?int {
		return $this->status;
	}

	public function setStatus(?int $status) : self {
		$this->status = $status;

		return $this;
	}

	public function getDone() : ?int {
		return $this->done;
	}

	public function setDone(?int $status) : self {
		$this->done = $status;

		return $this;
	}

	public function getPlayer() : ?string {
		return $this->player;
	}

	public function setPlayer(?string $login) : self {
		$this->player = $login;

		return $this;
	}

	public function getAmount() : float {
		return $this->amount;
	}

	public function setAmount(float $amount) : self {
		$this->amount = $amount;

		return $this;
	}

	public function getResponse() : ?string {
		return $this->response;
	}

	public function setResponse(?string $data) : self {
		$this->response = $data;

		return $this;
	}

	public function getData() : ?string {
		return $this->data;
	}

	public function setData(?string $data) : self {
		$this->data = $data;

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

	public function getItem() : ?Item {
		return $this->item;
	}

	public function setItem(?Item $item) : self {
		$this->item = $item;

		return $this;
	}

	public function getSection() : ?Section {
		return $this->section;
	}

	public function setSection(?Section $section) : self {
		$this->section = $section;

		return $this;
	}

	public function getCategory() : ?Category {
		return $this->category;
	}

	public function setCategory(?Category $category) : self {
		$this->category = $category;

		return $this;
	}

	public function getServer() : ?Server {
		return $this->server;
	}

	public function setServer(?Server $server) : self {
		$this->server = $server;

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

		if(!is_null($this->getItemID())){
			$and .= " AND `item_id` = '?'";
			$prepare[] = $this->getItemID();
		}

		if(!is_null($this->getSum())){
			$and .= " AND `sum` = '?'";
			$prepare[] = $this->getSum();
		}

		if(!is_null($this->getStatus())){
			$and .= " AND `status` = '?'";
			$prepare[] = $this->getStatus();
		}

		if(!is_null($this->getDone())){
			$and .= " AND `done` = '?'";
			$prepare[] = $this->getDone();
		}

		if(!is_null($this->getPromoID())){
			$and .= " AND `promo_id` = '?'";
			$prepare[] = $this->getPromoID();
		}

		if(!is_null($this->getPlayer())){
			$and .= " AND `player` = '?'";
			$prepare[] = $this->getPlayer();
		}

		$query = Database::prepare("SELECT * FROM `payments` WHERE 1 {$and} LIMIT 1", $prepare);


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

		//$id, $item_id, $sum, $status, $done, $player, $date_create, $date_update

		if(is_null($this->getItemID())){
			$this->setItemID(0);
		}

		if(is_null($this->getSum())){
			$this->setSum(0);
		}

		if(is_null($this->getStatus())){
			$this->setStatus(0);
		}

		if(is_null($this->getDone())){
			$this->setDone(0);
		}

		if(is_null($this->getPromoID())){
			$this->setPromoID(0);
		}

		if(is_null($this->getPlayer())){
			$this->setPlayer(0);
		}

		$insert = Database::prepare("INSERT INTO `payments`
										(`item_id`, `sum`, `status`, `done`, `promo_id`, `player`, `amount`, `data`, `response`, `date_create`, `date_update`)
									VALUES
										('?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?')", [
			$this->getItemID(), $this->getSum(), $this->getStatus(), $this->getDone(), $this->getPromoID(), $this->getPlayer(), $this->getAmount(),
			$this->getData(), $this->getResponse(), $this->getDateCreate(), $this->getDateUpdate()
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

		$update = Database::prepare("UPDATE `payments`
									SET `item_id` = '?', `sum` = '?', `status` = '?', `done` = '?', `promo_id` = '?', `player` = '?', `amount` = '?', `data` = '?', `response` = '?', `date_update` = '?'
									WHERE `id` = '?'
									LIMIT 1", [
			$this->getItemID(), $this->getSum(), $this->getStatus(), $this->getDone(), $this->getPromoID(), $this->getPlayer(), $this->getAmount(),
			$this->getData(), $this->getResponse(), $this->getDateUpdate(), $this->getID()
		]);

		if(!$update){
			Log::error('SQL Error in '.__FILE__.' on line '.__LINE__.' ('.Database::mysql()->error.')');

			return false;
		}

		return true;
	}

	public function delete(?int $id = null) : bool {
		if(!is_null($id)){ $this->setID(intval($id)); }

		$delete = Database::prepare("DELETE FROM `payments` WHERE `id` = '?' LIMIT 1", [$this->getID()]);

		if(!$delete){
			Log::error('SQL Error in '.__FILE__.' on line '.__LINE__.' ('.Database::mysql()->error.')');

			return false;
		}

		return true;
	}
}

?>