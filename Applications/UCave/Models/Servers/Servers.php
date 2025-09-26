<?php

namespace App\Models\Servers;

class Servers {

	const PAGINATION_LIMIT = 30;

	public static function Server(?array $import = null) : Server {
		return new Server($import);
	}

	public static function Listing() : Listing {
		return new Listing();
	}
}

?>