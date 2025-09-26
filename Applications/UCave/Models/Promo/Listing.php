<?php

namespace App\Models\Promo;

use App\Bootstrap;
use App\Models\Log;
use Component\Database\Database;

class Listing {
	private $count, $list, $pagination;

	private $sort = '`p`.`id` ASC';

	private $page = '/?admin/promo/&page={NUM}';

	public function setPage(string $page) : self {
		$this->page = $page;

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

		$this->pagination = Bootstrap::pagination(intval(@$_GET['page']), $this->page, $this->getCount(), Promo::PAGINATION_LIMIT);

		return $this;
	}

	public function pagination() : ?array {
		return $this->pagination;
	}

	public function getCount() : int {
		if(!is_null($this->count)){
			return $this->count;
		}

		$where = "";

		$prepare = [];

		$this->count = 0;

		$select = Database::prepare("SELECT COUNT(1) FROM `promo` WHERE 1 {$where}", $prepare);

		if(!$select){
			Log::error('SQL Error in '.__FILE__.' on line '.__LINE__.' ('.Database::mysql()->error.')');

			return $this->count;
		}

		$ar = $select->fetch_row();

		$this->count = intval(@$ar[0]);

		return $this->count;
	}

	/** @return Code[] */
	public function get() : array {
		if(!is_null($this->list)){
			return $this->list;
		}

		$this->list = [];

		$where = $other = "";

		$columns = "`p`.*";

		$prepare = [];

		if(!is_null($this->sort)){
			$other .= " ORDER BY {$this->sort}";
		}

		if(!is_null($this->pagination)){
			$other .= " LIMIT {$this->pagination['limit']} OFFSET {$this->pagination['offset']}";
		}

		$select = Database::prepare("SELECT {$columns}
									FROM `promo` AS `p`
									WHERE 1 {$where} {$other}", $prepare);

		if(!$select){
			Log::error('SQL Error in '.__FILE__.' on line '.__LINE__.' ('.Database::mysql()->error.')');

			return $this->list;
		}

		if(!$select->num_rows){
			return $this->list;
		}

		while($ar = $select->fetch_assoc()){
			$payment = Promo::Code($ar);

			$this->list[] = $payment;
		}

		return $this->list;
	}
}

?>