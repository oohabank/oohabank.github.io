<?php

namespace App\Models\Items;

use App\Bootstrap;
use App\Models\Categories\Categories;
use App\Models\Log;
use App\Models\Sections\Sections;
use App\Models\Servers\Servers;
use Component\Database\Database;

class Listing {
	private $count, $list, $pagination, $user_id_create, $section_id, $name, $category_id, $server_id, $surcharge, $prefix;

	private $sort = "`i`.`id` DESC";

	private $limit;

	public function setSurcharge(?int $value) : self {
		$this->surcharge = $value;

		return $this;
	}

	public function setPrefix(?int $value) : self {
		$this->prefix = $value;

		return $this;
	}

	public function setSectionID(?int $id) : self {
		$this->section_id = $id;

		return $this;
	}

	public function setCategoryID(?int $id) : self {
		$this->category_id = $id;

		return $this;
	}

	public function setServerID(?int $id) : self {
		$this->server_id = $id;

		return $this;
	}

	public function setName(?string $name) : self {
		$this->name = $name;

		return $this;
	}

	public function getLimit() : int {
		if(!is_int($this->limit)){
			$this->limit = Items::PAGINATION_LIMIT;
		}

		return $this->limit;
	}

	public function setLimit(int $limit) : self {
		$this->limit = ($limit < 1) ? 1 : $limit;

		return $this;
	}

	public function setCreatorID(?int $id) : self {
		$this->user_id_create = $id;

		return $this;
	}

	public function setSort(?string $sort) : self {
		$this->sort = $sort;

		return $this;
	}

	public function setPagination() : self {
		if(!is_null($this->pagination)){
			return $this->pagination;
		}

		$page = '/?admin/items/&page={NUM}';

		$this->pagination = Bootstrap::pagination(intval(@$_GET['page']), $page, $this->getCount(), $this->getLimit());

		return $this;
	}

	public function getPagination() : ?array {
		return $this->pagination;
	}

	public function getCount() : int {
		if(!is_null($this->count)){
			return $this->count;
		}

		$and = "";
		$prepare = [];

		if(!is_null($this->user_id_create)){
			$and .= " AND `user_id_create` = '?'";
			$prepare[] = $this->user_id_create;
		}

		if(!is_null($this->section_id)){
			$and .= " AND `section_id` = '?'";
			$prepare[] = $this->section_id;
		}

		if(!is_null($this->category_id)){
			$and .= " AND `category_id` = '?'";
			$prepare[] = $this->category_id;
		}

		if(!is_null($this->server_id)){
			$and .= " AND `server_id` = '?'";
			$prepare[] = $this->server_id;
		}

		if(!is_null($this->surcharge)){
			$and .= " AND `surcharge` = '?'";
			$prepare[] = $this->surcharge;
		}

		if(!is_null($this->prefix)){
			$and .= " AND `prefix` = '?'";
			$prepare[] = $this->prefix;
		}

		if(!is_null($this->name)){
			$and .= " AND `name` = '?'";
			$prepare[] = $this->name;
		}

		if(!empty($this->search)){
			$search = addcslashes(Database::mysql()->escape_string($this->search), '_%$[]');
			$and .= " AND `title` LIKE '%{$search}%'";
		}

		$this->count = 0;

		$select = Database::prepare("SELECT COUNT(1) FROM `items` WHERE 1 {$and}");

		if(!$select){
			Log::error('SQL Error in '.__FILE__.' on line '.__LINE__.' ('.Database::mysql()->error.')');

			return $this->count;
		}

		$ar = $select->fetch_row();

		$this->count = intval(@$ar[0]);

		return $this->count;
	}

	/** @return Item[] */
	public function get() : array {
		if(!is_null($this->list)){
			return $this->list;
		}

		$this->list = [];

		$columns = "`i`.*";
		$and = $other = "";
		$prepare = [];

		if(!is_null($this->user_id_create)){
			$and .= " AND `i`.`user_id_create` = '?'";
			$prepare[] = $this->user_id_create;
		}

		if(!is_null($this->section_id)){
			$and .= " AND `i`.`section_id` = '?'";
			$prepare[] = $this->section_id;
		}

		if(!is_null($this->category_id)){
			$and .= " AND `i`.`category_id` = '?'";
			$prepare[] = $this->category_id;
		}

		if(!is_null($this->server_id)){
			$and .= " AND `i`.`server_id` = '?'";
			$prepare[] = $this->server_id;
		}

		if(!is_null($this->surcharge)){
			$and .= " AND `i`.`surcharge` = '?'";
			$prepare[] = $this->surcharge;
		}

		if(!is_null($this->prefix)){
			$and .= " AND `i`.`prefix` = '?'";
			$prepare[] = $this->prefix;
		}

		if(!is_null($this->name)){
			$and .= " AND `i`.`name` = '?'";
			$prepare[] = $this->name;
		}

		if(!empty($this->search)){
			$search = addcslashes(Database::mysql()->escape_string($this->search), '_%$[]');
			$and .= " AND `i`.`title` LIKE '%{$search}%'";
		}

		$sectionalias = Database::alias([
			'id', 'title', 'text',
			'date_create', 'date_update',
			'user_id_create', 'user_id_update'
		], 's');

		$columns .= ','.implode(', ', $sectionalias);

		$categoryalias = Database::alias([
			'id', 'section_id', 'title', 'text',
			'date_create', 'date_update',
			'user_id_create', 'user_id_update'
		], 'c');

		$columns .= ','.implode(', ', $categoryalias);

		$serveralias = Database::alias([
			'id', 'title', 'text',
			'rcon_host', 'rcon_port', 'rcon_password',
			'date_create', 'date_update',
			'user_id_create', 'user_id_update'
		], 'srv');

		$columns .= ','.implode(', ', $serveralias);

		if(!is_null($this->sort)){
			$other .= " ORDER BY {$this->sort}";
		}

		if(!is_null($this->pagination)){
			$other .= " LIMIT {$this->pagination['limit']} OFFSET {$this->pagination['offset']}";
		}

		$select = Database::prepare("SELECT {$columns}
									FROM `items` AS `i`
									LEFT JOIN `sections` AS `s`
										ON `s`.`id` = `i`.`section_id`
									LEFT JOIN `categories` AS `c`
										ON `c`.`id` = `i`.`category_id`
									LEFT JOIN `servers` AS `srv`
										ON `srv`.`id` = `i`.`server_id`
									WHERE 1 {$and} {$other}", $prepare);

		if(!$select){
			Log::error('SQL Error in '.__FILE__.' on line '.__LINE__.' ('.Database::mysql()->error.')');

			return $this->list;
		}

		if(!$select->num_rows){
			return $this->list;
		}

		while($ar = $select->fetch_assoc()){
			$item = Items::Item($ar);
			$item->setSection(Sections::Section(Database::unalias($sectionalias, $ar)));
			$item->setServer(Servers::Server(Database::unalias($serveralias, $ar)));
			$item->setCategory(Categories::Category(Database::unalias($categoryalias, $ar)));

			$this->list[] = $item;
		}

		return $this->list;
	}
}

?>