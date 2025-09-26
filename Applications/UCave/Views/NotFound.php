<?php

namespace App\Views;

use App\Bootstrap;

class NotFound {
	public function index() {

		header("HTTP/1.0 404 Not Found");

		echo Bootstrap::templaterEnv()->render('Resources/NotFound/index.tpl');
	}
}

?>