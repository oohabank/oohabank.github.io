<?php

namespace App\Models\Sections;

use App\Bootstrap;
use App\Models\Log;
use Component\Database\Database;

class Listing {
	private $count, $list, $pagination, $user_id_create;

	private $sort = "`s`.`id` DESC";

	private $limit;

	public function getLimit() : int {
		if(!is_int($this->limit)){
			$this->limit = Sections::PAGINATION_LIMIT;
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

		$page = '/?admin/sections/&page={NUM}';

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

		if(!empty($this->search)){
			$search = addcslashes(Database::mysql()->escape_string($this->search), '_%$[]');
			$and .= " AND `title` LIKE '%{$search}%'";
		}

		$this->count = 0;

		$select = Database::prepare("SELECT COUNT(1) FROM `sections` WHERE 1 {$and}");

		if(!$select){
			Log::error('SQL Error in '.__FILE__.' on line '.__LINE__.' ('.Database::mysql()->error.')');

			return $this->count;
		}

		$ar = $select->fetch_row();

		$this->count = intval(@$ar[0]);

		return $this->count;
	}

	/** @return Section[] */
	public function get() : array {
		if(!is_null($this->list)){
			return $this->list;
		}

		$this->list = [];

		$and = $other = "";
		$prepare = [];

		if(!is_null($this->user_id_create)){
			$and .= " AND `s`.`user_id_create` = '?'";
			$prepare[] = $this->user_id_create;
		}

		if(!empty($this->search)){
			$search = addcslashes(Database::mysql()->escape_string($this->search), '_%$[]');
			$and .= " AND `s`.`title` LIKE '%{$search}%'";
		}

		if(!is_null($this->sort)){
			$other .= " ORDER BY {$this->sort}";
		}

		if(!is_null($this->pagination)){
			$other .= " LIMIT {$this->pagination['limit']} OFFSET {$this->pagination['offset']}";
		}

		$select = Database::prepare("SELECT `s`.* FROM `sections` AS `s` WHERE 1 {$and} {$other}", $prepare);

		if(!$select){
			Log::error('SQL Error in '.__FILE__.' on line '.__LINE__.' ('.Database::mysql()->error.')');

			return $this->list;
		}

		if(!$select->num_rows){
			return $this->list;
		}

		while($ar = $select->fetch_assoc()){
			$this->list[] = Sections::Section($ar);
		}

		return $this->list;
	}
}

?>