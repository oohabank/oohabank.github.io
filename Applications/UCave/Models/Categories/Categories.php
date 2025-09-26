<?php

namespace App\Models\Categories;

class Categories {

	const PAGINATION_LIMIT = 30;

	public static function Category(?array $import = null) : Category {
		return new Category($import);
	}

	public static function Listing() : Listing {
		return new Listing();
	}
}

?>