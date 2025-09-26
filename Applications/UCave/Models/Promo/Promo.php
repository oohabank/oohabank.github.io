<?php

namespace App\Models\Promo;

class Promo {

	const PAGINATION_LIMIT = 20;

	public static function Code(?array $import = null) : Code {
		return new Code($import);
	}

	public static function Listing() : Listing {
		return new Listing();
	}
}

?>