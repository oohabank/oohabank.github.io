<?php

namespace App\Models\Items;

class Items {

	const PAGINATION_LIMIT = 30;

	public static function Item(?array $import = null) : Item {
		return new Item($import);
	}

	public static function Listing() : Listing {
		return new Listing();
	}
}

?>