<?php

namespace App\Models\Categories;

use App\Models\Log;
use App\Models\Sections\Section;
use App\Models\Users\Users;
use Component\Database\Database;

class Category {
	private $id, $section_id, $user_id_create, $user_id_update, $date_create, $date_update, $section;

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

		if(isset($data['section_id'])){
			$this->setSectionID(intval($data['section_id']));
		}

		if(isset($data['title'])){
			$this->setTitle($data['title']);
		}

		if(isset($data['text'])){
			$this->setText($data['text']);
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

	public function getSectionID() : ?int {
		return $this->section_id;
	}

	public function setSectionID(?int $id) : self {
		$this->section_id = $id;

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

	public function setSection(?Section $section) : self {
		$this->section = $section;

		return $this;
	}

	public function getSection() : ?Section {
		return $this->section;
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

		$query = Database::prepare("SELECT *
									FROM `categories`
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

		$insert = Database::prepare("INSERT INTO `categories`
										(`section_id`, `title`, `text`, `date_create`, `date_update`,
										`user_id_create`, `user_id_update`)
									VALUES
										('?', '?', '?', '?', '?', '?', '?')", [
										$this->getSectionID(), $this->getTitle(), $this->getText(),
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

		$update = Database::prepare("UPDATE `categories`
									SET `section_id` = '?', `title` = '?', `text` = '?', `date_update` = '?', `user_id_update` = '?'
									WHERE `id` = '?'
									LIMIT 1", [
			$this->getSectionID(), $this->getTitle(), $this->getText(), $this->getDateUpdate(), $this->getUpdaterID(), $this->getID()
		]);

		if(!$update){
			Log::error('SQL Error in '.__FILE__.' on line '.__LINE__.' ('.Database::mysql()->error.')');

			return false;
		}

		return true;
	}

	public function delete(?int $id = null) : bool {
		if(!is_null($id)){ $this->setID(intval($id)); }

		$delete = Database::prepare("DELETE FROM `categories` WHERE `id` = '?' LIMIT 1", [$this->getID()]);

		if(!$delete){
			Log::error('SQL Error in '.__FILE__.' on line '.__LINE__.' ('.Database::mysql()->error.')');

			return false;
		}

		return true;
	}
}

?>