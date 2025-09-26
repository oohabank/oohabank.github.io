<?php

namespace App\Models\Tokens;

use App\Models\Log;
use Component\Database\Database;

class Listing {
	private $count, $list, $pagination;

	private $sort = ['`t`.`id`' => 'DESC'];

	public function setSort(?array $sort) : self {
		$this->sort = $sort;

		return $this;
	}

	public function setPagination() : self {
		if(!is_null($this->pagination)){
			return $this->pagination;
		}

		$this->pagination = Bootstrap::pagination(intval(@$_GET['page']), '/?q=admin&p=promo&page={NUM}', $this->count(), Tokens::PAGINATION_LIMIT);

		return $this;
	}

	public function pagination() : ?array {
		return $this->pagination;
	}

	public function count() : int {
		if(!is_null($this->count)){
			return $this->count;
		}

		$where = $values = [];

		$this->count = 0;

		$select = Database::select()
			->columns(['COUNT(1)'])
			->from('user_tokens')
			->where($where, $values);

		if(!$select->execute()){
			Log::error('SQL Error in '.__FILE__.' on line '.__LINE__.' ('.$select->getError().')');

			return $this->count;
		}

		$ar = $select->getArray();

		$this->count = intval(@$ar[0][0]);

		return $this->count;
	}

	/** @return Token[] */
	public function get() : array {
		if(!is_null($this->list)){
			return $this->list;
		}

		$this->list = [];

		$where = $values = [];

		$select = Database::select()
			->columns(['`t`.*'])
			->from(['t' => 'user_tokens'])
			->where($where, $values);

		if(!is_null($this->sort)){
			$select->order($this->sort);
		}

		if(!is_null($this->pagination)){
			$select->limit($this->pagination['limit'])
				->offset($this->pagination['offset']);
		}

		if(!$select->execute()){
			Log::error('SQL Error in '.__FILE__.' on line '.__LINE__.' ('.$select->getError().')');

			return $this->list;
		}

		if(!$select->getNum()){
			return $this->list;
		}

		foreach($select->getAssoc() as $ar){
			$this->list[] = Tokens::Token($ar);
		}

		return $this->list;
	}
}

?>