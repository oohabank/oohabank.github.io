<?php

namespace App\Models\Tokens;

class Tokens {

	const PAGINATION_LIMIT = 20;

	public static function Token(?array $import = null) : Token {
		return new Token($import);
	}

	public static function Listing() : Listing {
		return new Listing();
	}
}

?>