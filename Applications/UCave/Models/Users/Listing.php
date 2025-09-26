<?php

namespace App\Models\Users;

use App\Bootstrap;
use App\Models\Log;
use Component\Database\Database;

class Listing {
	private $count, $list, $pagination;

	private $sort = '`u`.`id` DESC';

	private $search = '';

	public function setSearch($search) : self {
		$this->search = !is_string($search) ? '' : trim($search);

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

		$page = '/?admin/users';

		if(!empty($this->search)){
			$page = "/?admin/users&search={$this->search}";
		}

		$page .= '&page={NUM}';

		$this->pagination = Bootstrap::pagination(intval(@$_GET['page']), $page, $this->getCount(), Users::PAGINATION_LIMIT);

		return $this;
	}

	public function pagination() : ?array {
		return $this->pagination;
	}

	public function getCount() : int {
		if(!is_null($this->count)){
			return $this->count;
		}

		$where = " WHERE 1";

		$prepare = [];

		$this->count = 0;

		if(!empty($this->search)){
			$search = addcslashes(Database::mysql()->escape_string($this->search), '_%$[]');
			$where .= " AND (`email` LIKE '%{$search}%' OR `login` LIKE '%{$search}%')";
		}

		$select = Database::prepare("SELECT COUNT(1) FROM `users` {$where}", $prepare);

		if(!$select){
			Log::error('SQL Error in '.__FILE__.' on line '.__LINE__.' ('.Database::mysql()->error.')');

			return $this->count;
		}

		$ar = $select->fetch_row();

		$this->count = intval(@$ar[0]);

		return $this->count;
	}

	/** @return User[] */
	public function get() : array {
		if(!is_null($this->list)){
			return $this->list;
		}

		$this->list = [];

		$where = $other = "";

		$prepare = [];

		if(!empty($this->search)){
			$search = addcslashes(Database::mysql()->escape_string($this->search), '_%$[]');
			$where .= " AND (`u`.`email` LIKE '%{$search}%' OR `u`.`login` LIKE '%{$search}%')";
		}

		if(!is_null($this->sort)){
			$other .= " ORDER BY {$this->sort}";
		}

		if(!is_null($this->pagination)){
			$other .= " LIMIT {$this->pagination['limit']} OFFSET {$this->pagination['offset']}";
		}

		$select = Database::prepare("SELECT `u`.*
									FROM `users` AS `u`
									WHERE 1 {$where} {$other}", $prepare);

		if(!$select){
			Log::error('SQL Error in '.__FILE__.' on line '.__LINE__.' ('.Database::mysql()->error.')');

			return $this->list;
		}

		if(!$select->num_rows){
			return $this->list;
		}

		while($ar = $select->fetch_assoc()){
			$this->list[] = Users::User($ar);
		}

		return $this->list;
	}
}

?>