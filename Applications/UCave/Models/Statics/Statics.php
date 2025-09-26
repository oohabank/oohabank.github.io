<?php

namespace App\Models\Statics;

class Statics {

	const PAGINATION_LIMIT = 20;

	public static function Page(?array $import = null) : Page {
		return new Page($import);
	}

	public static function Listing() : Listing {
		return new Listing();
	}
}

?>