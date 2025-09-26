<?php

namespace App\Models\Categories;

use App\Bootstrap;
use App\Models\Log;
use App\Models\Sections\Sections;
use Component\Database\Database;

class Listing {
	private $count, $list, $pagination, $user_id_create, $section_id;

	private $sort = "`c`.`id` DESC";

	private $limit;

	public function setSectionID(?int $id) : self {
		$this->section_id = $id;

		return $this;
	}

	public function getLimit() : int {
		if(!is_int($this->limit)){
			$this->limit = Categories::PAGINATION_LIMIT;
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

		$page = '/?admin/categories/&page={NUM}';

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

		if(!empty($this->search)){
			$search = addcslashes(Database::mysql()->escape_string($this->search), '_%$[]');
			$and .= " AND `title` LIKE '%{$search}%'";
		}

		$this->count = 0;

		$select = Database::prepare("SELECT COUNT(1) FROM `categories` WHERE 1 {$and}");

		if(!$select){
			Log::error('SQL Error in '.__FILE__.' on line '.__LINE__.' ('.Database::mysql()->error.')');

			return $this->count;
		}

		$ar = $select->fetch_row();

		$this->count = intval(@$ar[0]);

		return $this->count;
	}

	/** @return Category[] */
	public function get() : array {
		if(!is_null($this->list)){
			return $this->list;
		}

		$this->list = [];

		$columns = "`c`.*";
		$and = $other = "";
		$prepare = [];

		if(!is_null($this->section_id)){
			$and .= " AND `c`.`section_id` = '?'";
			$prepare[] = $this->section_id;
		}

		if(!is_null($this->user_id_create)){
			$and .= " AND `c`.`user_id_create` = '?'";
			$prepare[] = $this->user_id_create;
		}

		if(!empty($this->search)){
			$search = addcslashes(Database::mysql()->escape_string($this->search), '_%$[]');
			$and .= " AND `c`.`title` LIKE '%{$search}%'";
		}

		$useralias = Database::alias([
			'id', 'title', 'text',
			'date_create', 'date_update',
			'user_id_create', 'user_id_update'
		], 's');

		$columns .= ','.implode(', ', $useralias);

		if(!is_null($this->sort)){
			$other .= " ORDER BY {$this->sort}";
		}

		if(!is_null($this->pagination)){
			$other .= " LIMIT {$this->pagination['limit']} OFFSET {$this->pagination['offset']}";
		}

		$select = Database::prepare("SELECT {$columns}
									FROM `categories` AS `c`
									LEFT JOIN `sections` AS `s`
										ON `s`.`id` = `c`.`section_id`
									WHERE 1 {$and} {$other}", $prepare);

		if(!$select){
			Log::error('SQL Error in '.__FILE__.' on line '.__LINE__.' ('.Database::mysql()->error.')');

			return $this->list;
		}

		if(!$select->num_rows){
			return $this->list;
		}

		while($ar = $select->fetch_assoc()){
			$category = Categories::Category($ar)->setSection(Sections::Section(Database::unalias($useralias, $ar)));

			$this->list[] = $category;
		}

		return $this->list;
	}
}

?>