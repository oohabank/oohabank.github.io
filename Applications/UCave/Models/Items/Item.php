<?php

namespace App\Models\Items;

use App\Bootstrap;
use App\Models\Categories\Category;
use App\Models\Log;
use App\Models\Sections\Section;
use App\Models\Servers\Server;
use App\Models\Users\Users;
use Component\Database\Database;

class Item {
	private $id, $name, $section_id, $category_id, $server_id, $user_id_create, $user_id_update, $date_create, $date_update, $section, $category, $server, $surcharge, $prefix, $prefix_min, $prefix_max;

	private $command = '';

	private $image = '';

	private $title = '';

	private $text = '';

	private $price = 0;

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

		if(isset($data['section_id'])){
			$this->setSectionID(intval($data['section_id']));
		}

		if(isset($data['category_id'])){
			$this->setCategoryID(intval($data['category_id']));
		}

		if(isset($data['server_id'])){
			$this->setServerID(intval($data['server_id']));
		}

		if(isset($data['price'])){
			$this->setPrice(floatval($data['price']));
		}

		if(isset($data['name'])){
			$this->setName($data['name']);
		}

		if(isset($data['command'])){
			$this->setCommand($data['command']);
		}

		if(isset($data['image'])){
			$this->setImage($data['image']);
		}

		if(isset($data['title'])){
			$this->setTitle($data['title']);
		}

		if(isset($data['text'])){
			$this->setText($data['text']);
		}

		if(isset($data['surcharge'])){
			$this->setSurcharge(intval($data['surcharge']));
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

		if(isset($data['prefix'])){
			$this->setPrefix(intval($data['prefix']));
		}

		if(isset($data['prefix_min'])){
			$this->setPrefixMin(intval($data['prefix_min']));
		}

		if(isset($data['prefix_max'])){
			$this->setPrefixMax(intval($data['prefix_max']));
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

	public function getSectionID() : ?int {
		return $this->section_id;
	}

	public function setSectionID(?int $id) : self {
		$this->section_id = $id;

		return $this;
	}

	public function getCategoryID() : ?int {
		return $this->category_id;
	}

	public function setCategoryID(?int $id) : self {
		$this->category_id = $id;

		return $this;
	}

	public function getServerID() : ?int {
		return $this->server_id;
	}

	public function setServerID(?int $id) : self {
		$this->server_id = $id;

		return $this;
	}

	public function getSurcharge() : ?int {
		return $this->surcharge;
	}

	public function setSurcharge(?int $value) : self {
		$this->surcharge = $value;

		return $this;
	}

	public function getPrice() : float {
		return $this->price;
	}

	public function setPrice(float $price) : self {
		$this->price = $price;

		return $this;
	}

	public function getName() : ?string {
		return $this->name;
	}

	public function setName(?string $name) : self {
		$this->name = $name;

		return $this;
	}

	public function getCommand() : string {
		return $this->command;
	}

	public function setCommand(string $command) : self {
		$this->command = $command;

		return $this;
	}

	public function getImage() : string {
		return $this->image;
	}

	public function setImage(string $url) : self {
		$this->image = $url;

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

	public function getPrefix() : ?int {
		return $this->prefix;
	}

	public function setPrefix(?int $value) : self {
		$this->prefix = $value;

		return $this;
	}

	public function getPrefixMin() : ?int {
		return $this->prefix_min;
	}

	public function setPrefixMin(?int $value) : self {
		$this->prefix_min = $value;

		return $this;
	}

	public function getPrefixMax() : ?int {
		return $this->prefix_max;
	}

	public function setPrefixMax(?int $value) : self {
		$this->prefix_max = $value;

		return $this;
	}

	public function setSection(?Section $section) : self {
		$this->section = $section;

		return $this;
	}

	public function getSection() : ?Section {
		return $this->section;
	}

	public function setCategory(?Category $category) : self {
		$this->category = $category;

		return $this;
	}

	public function getCategory() : ?Category {
		return $this->category;
	}

	public function setServer(?Server $server) : self {
		$this->server = $server;

		return $this;
	}

	public function getServer() : ?Server {
		return $this->server;
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

		if(!is_null($this->getSectionID())){
			$and .= " AND `section_id` = '?'";
			$prepare[] = $this->getSectionID();
		}

		if(!is_null($this->getCategoryID())){
			$and .= " AND `category_id` = '?'";
			$prepare[] = $this->getCategoryID();
		}

		if(!is_null($this->getServerID())){
			$and .= " AND `server_id` = '?'";
			$prepare[] = $this->getServerID();
		}

		if(!is_null($this->getSurcharge())){
			$and .= " AND `surcharge` = '?'";
			$prepare[] = $this->getSurcharge();
		}

		if(!is_null($this->getName())){
			$and .= " AND `name` = '?'";
			$prepare[] = $this->getName();
		}

		if(!is_null($this->getPrefix())){
			$and .= " AND `prefix` = '?'";
			$prepare[] = $this->getPrefix();
		}

		if(!is_null($this->getPrefixMin())){
			$and .= " AND `prefix_min` = '?'";
			$prepare[] = $this->getPrefixMin();
		}

		if(!is_null($this->getPrefixMax())){
			$and .= " AND `prefix_max` = '?'";
			$prepare[] = $this->getPrefixMax();
		}

		$query = Database::prepare("SELECT *
									FROM `items`
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

		if(is_null($this->getSectionID())){
			$this->setSectionID(1);
		}

		if(is_null($this->getCategoryID())){
			$this->setCategoryID(1);
		}

		if(is_null($this->getServerID())){
			$this->setServerID(0);
		}

		if(is_null($this->getSurcharge())){
			$this->setSurcharge(0);
		}

		if(is_null($this->getName())){
			$this->setName(Bootstrap::toLatin($this->getTitle()));
		}

		if(is_null($this->getPrefix())){
			$this->setPrefix(0);
		}

		if(is_null($this->getPrefixMin())){
			$this->setPrefixMin(0);
		}

		if(is_null($this->getPrefixMax())){
			$this->setPrefixMax(0);
		}

		$insert = Database::prepare("INSERT INTO `items`
										(`title`, `name`, `text`, `image`, `price`,
										`section_id`, `category_id`, `server_id`,
										`command`, `surcharge`, `date_create`, `date_update`,
										`user_id_create`, `user_id_update`, `prefix`, `prefix_min`, `prefix_max`)
									VALUES
										('?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?')", [
										$this->getTitle(), $this->getName(), $this->getText(),
										$this->getImage(), $this->getPrice(), $this->getSectionID(),
										$this->getCategoryID(), $this->getServerID(), $this->getCommand(),
										$this->getSurcharge(), $this->getDateCreate(), $this->getDateUpdate(),
										$this->getCreatorID(), $this->getUpdaterID(), $this->getPrefix(),
										$this->getPrefixMin(), $this->getPrefixMax()
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

		$update = Database::prepare("UPDATE `items`
									SET `title` = '?', `name` = '?', `text` = '?', `image` = '?', `price` = '?',
										`section_id` = '?', `category_id` = '?', `server_id` = '?', `command` = '?',
										`surcharge` = '?', `date_update` = '?', `user_id_update` = '?', `prefix` = '?',
										`prefix_min` = '?', `prefix_max` = '?'
									WHERE `id` = '?'
									LIMIT 1", [
			$this->getTitle(), $this->getName(), $this->getText(),
			$this->getImage(), $this->getPrice(), $this->getSectionID(),
			$this->getCategoryID(), $this->getServerID(), $this->getCommand(),
			$this->getSurcharge(), $this->getDateUpdate(), $this->getUpdaterID(),
			$this->getPrefix(), $this->getPrefixMin(), $this->getPrefixMax(),
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

		$delete = Database::prepare("DELETE FROM `items` WHERE `id` = '?' LIMIT 1", [$this->getID()]);

		if(!$delete){
			Log::error('SQL Error in '.__FILE__.' on line '.__LINE__.' ('.Database::mysql()->error.')');

			return false;
		}

		return true;
	}
}

?>