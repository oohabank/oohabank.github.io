<?php

namespace App\Views;

use App\Bootstrap;
use App\Models\Statics\Statics as S;

class Statics {
	public function index() {

		$name = isset($_GET['p']) ? $_GET['p'] : '';

		$page = S::Page()->setName($name);

		if(!$page->get()){
			Bootstrap::redirect();
		}

		echo Bootstrap::templaterEnv()->createTemplate($page->getContent())->render([
			'page' => $page
		]);
	}
}

?>