<?php

namespace App\Models\Payments;

use App\Bootstrap;
use App\Models\Categories\Categories;
use App\Models\Items\Items;
use App\Models\Log;
use App\Models\Sections\Sections;
use App\Models\Servers\Servers;
use Component\Database\Database;

class Listing {
	private $count, $list, $pagination, $status, $limit, $promo_id, $item_id, $player, $item_ids;

	private $sort = '`p`.`id` DESC';

	private $page = '/?admin/payments/&page={NUM}';

	public function setLimit(?int $amount) : self {
		$this->limit = $amount;

		return $this;
	}

	public function setItemIDs(?array $ids) : self {
		$this->item_ids = $ids;

		return $this;
	}

	public function setItemID(?int $id) : self {
		$this->item_id = $id;

		return $this;
	}

	public function setPromoID(?int $id) : self {
		$this->promo_id = $id;

		return $this;
	}

	public function setPlayer(?string $player) : self {
		$this->player = $player;

		return $this;
	}

	public function setStatus(?int $value) : self {
		$this->status = $value;

		return $this;
	}

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

		$this->pagination = Bootstrap::pagination(intval(@$_GET['page']), $this->page, $this->getCount(), Payments::PAGINATION_LIMIT);

		return $this;
	}

	public function getPagination() : ?array {
		return $this->pagination;
	}

	public function getCount() : int {
		if(!is_null($this->count)){
			return $this->count;
		}

		$where = "";

		$prepare = [];

		$this->count = 0;

		if(!is_null($this->player)){
			$where .= " AND `player` = '?'";
			$prepare[] = $this->player;
		}

		if(!is_null($this->status)){
			$where .= " AND `status` = '?'";
			$prepare[] = $this->status;
		}

		if(!is_null($this->promo_id)){
			$where .= " AND `promo_id` = '?'";
			$prepare[] = $this->promo_id;
		}

		if(!is_null($this->item_id)){
			$where .= " AND `item_id` > '?'";
			$prepare[] = $this->item_id;
		}

		if(!is_null($this->item_ids) && !empty($this->item_ids)){
			$where .= " AND `item_id` IN (?)";
			$prepare[] = implode(',', $this->item_ids);
		}

		$select = Database::prepare("SELECT COUNT(1) FROM `payments` WHERE 1 {$where}", $prepare);

		if(!$select){
			Log::error('SQL Error in '.__FILE__.' on line '.__LINE__.' ('.Database::mysql()->error.')');

			return $this->count;
		}

		$ar = $select->fetch_row();

		$this->count = intval(@$ar[0]);

		return $this->count;
	}

	/** @return Payment[] */
	public function get() : array {
		if(!is_null($this->list)){
			return $this->list;
		}

		$this->list = [];

		$where = $other = "";

		$columns = "`p`.*";

		$prepare = [];

		if(!is_null($this->player)){
			$where .= " AND `p`.`player` = '?'";
			$prepare[] = $this->player;
		}

		if(!is_null($this->status)){
			$where .= " AND `p`.`status` = '?'";
			$prepare[] = $this->status;
		}

		if(!is_null($this->promo_id)){
			$where .= " AND `p`.`promo_id` = '?'";
			$prepare[] = $this->promo_id;
		}

		if(!is_null($this->item_id)){
			$where .= " AND `p`.`item_id` > '?'";
			$prepare[] = $this->item_id;
		}

		if(!is_null($this->item_ids) && !empty($this->item_ids)){
			$where .= " AND `p`.`item_id` IN (?)";
			$prepare[] = implode(',', $this->item_ids);
		}

		$itemalias = Database::alias([
			'id', 'title', 'name', 'text', 'image', 'price', 'section_id', 'server_id', 'category_id', 'command', 'date_create', 'date_update', 'user_id_create', 'user_id_update'
		], 'i');

		$columns .= ','.implode(', ', $itemalias);

		$serveralias = Database::alias([
			'id', 'title', 'text', 'rcon_host', 'rcon_port', 'rcon_password', 'date_create', 'date_update', 'user_id_create', 'user_id_update'
		], 'srv');

		$columns .= ','.implode(', ', $serveralias);

		$sectionalias = Database::alias([
			'id', 'title', 'text', 'date_create', 'date_update', 'user_id_create', 'user_id_update'
		], 's');

		$columns .= ','.implode(', ', $sectionalias);

		$categoryalias = Database::alias([
			'id', 'title', 'section_id', 'text', 'date_create', 'date_update', 'user_id_create', 'user_id_update'
		], 'c');

		$columns .= ','.implode(', ', $categoryalias);

		if(!is_null($this->sort)){
			$other .= " ORDER BY {$this->sort}";
		}

		if(!is_null($this->pagination)){
			$other .= " LIMIT {$this->pagination['limit']} OFFSET {$this->pagination['offset']}";
		}elseif(!is_null($this->limit)){
			$other .= " LIMIT {$this->limit}";
		}

		$sql = "SELECT {$columns}
									FROM `payments` AS `p`
									LEFT JOIN `items` AS `i`
										ON `i`.`id` = `p`.`item_id`
									LEFT JOIN `servers` AS `srv`
										ON `srv`.`id` = `i`.`server_id`
									LEFT JOIN `sections` AS `s`
										ON `s`.`id` = `i`.`section_id`
									LEFT JOIN `categories` AS `c`
										ON `c`.`id` = `i`.`category_id`
									WHERE 1 {$where} {$other}";

		$select = Database::prepare($sql, $prepare);

		if(!$select){
			Log::error('SQL Error in '.__FILE__.' on line '.__LINE__.' ('.Database::mysql()->error.')');

			return $this->list;
		}

		if(!$select->num_rows){
			return $this->list;
		}

		while($ar = $select->fetch_assoc()){
			$payment = Payments::Payment($ar)
				->setItem(Items::Item(Database::unalias($itemalias, $ar)))
				->setServer(Servers::Server(Database::unalias($serveralias, $ar)))
				->setCategory(Categories::Category(Database::unalias($categoryalias, $ar)))
				->setSection(Sections::Section(Database::unalias($sectionalias, $ar)));

			$this->list[] = $payment;
		}

		return $this->list;
	}
}

?>