<?php

namespace App\Models\Statics;

use App\Bootstrap;
use App\Models\Log;
use Component\Database\Database;

class Listing {
	private $count, $list, $pagination, $name;

	private $prepare = [];

	private $where = "";

	private $sort = '`s`.`id` ASC';

	public function setName(?string $name) : self {
		$this->name = $name;

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

		$this->pagination = Bootstrap::pagination(intval(@$_GET['page']), '/?admin/statics/&page={NUM}', $this->getCount(), Statics::PAGINATION_LIMIT);

		return $this;
	}

	public function pagination() : ?array {
		return $this->pagination;
	}

	public function getCount() : int {
		if(!is_null($this->count)){
			return $this->count;
		}

		$where = $this->where;

		$prepare = $this->prepare;

		$this->count = 0;

		if(!is_null($this->name)){
			$where .= " AND `name` = '?'";
			$prepare[] = $this->name;
		}

		$select = Database::prepare("SELECT COUNT(1) FROM `statics` WHERE 1 {$where}", $prepare);

		if(!$select){
			Log::error('SQL Error in '.__FILE__.' on line '.__LINE__.' ('.Database::mysql()->error.')');

			return $this->count;
		}

		$ar = $select->fetch_row();

		$this->count = intval(@$ar[0]);

		return $this->count;
	}

	/** @return Page[] */
	public function get() : array {
		if(!is_null($this->list)){
			return $this->list;
		}

		$this->list = [];

		$where = $other = "";

		$prepare = [];

		if(!is_null($this->name)){
			$where .= " AND `s`.`name` = '?'";
			$prepare[] = $this->name;
		}

		if(!is_null($this->sort)){
			$other .= " ORDER BY {$this->sort}";
		}

		if(!is_null($this->pagination)){
			$other .= " LIMIT {$this->pagination['limit']} OFFSET {$this->pagination['offset']}";
		}

		$select = Database::prepare("SELECT `s`.*
									FROM `statics` AS `s`
									WHERE 1 {$where} {$other}", $prepare);

		if(!$select){
			Log::error('SQL Error in '.__FILE__.' on line '.__LINE__.' ('.Database::mysql()->error.')');

			return $this->list;
		}

		if(!$select->num_rows){
			return $this->list;
		}

		while($ar = $select->fetch_assoc()){
			$this->list[] = Statics::Page($ar);
		}

		return $this->list;
	}
}

?>