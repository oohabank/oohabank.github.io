<?php

namespace App\Models\Sections;

class Sections {

	const PAGINATION_LIMIT = 30;

	public static function Section(?array $import = null) : Section {
		return new Section($import);
	}

	public static function Listing() : Listing {
		return new Listing();
	}
}

?>