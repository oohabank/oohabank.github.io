<?php

namespace App\Views;

use App\Bootstrap;

class Donate {

	public function index() {

		echo Bootstrap::templaterEnv()->render('Resources/Donate/index.tpl');
	}
}

?>