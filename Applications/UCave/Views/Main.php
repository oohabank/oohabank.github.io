<?php

namespace App\Views;

use App\Bootstrap;
use App\Models\Categories\Categories;
use App\Models\Items\Items;
use App\Models\Sections\Sections;

class Main {

	public function index() {

		$sections = Sections::Listing()->setSort('`s`.`id` ASC')->get();

		$items = Items::Listing()->setSort('`i`.`price` ASC')->get();

		echo Bootstrap::templaterEnv()->render('Resources/Main/index.tpl', [
			'sections' => $sections,
			'categories' => Bootstrap::array_group(Categories::Listing()->setSort('`c`.`id` ASC')->get(), 'getSectionID', 'object'),
			'itemsSections' => Bootstrap::array_group($items, 'getSectionID', 'object'),
			'itemsCategories' => Bootstrap::array_group($items, 'getCategoryID', 'object'),
		]);
	}
}

?>