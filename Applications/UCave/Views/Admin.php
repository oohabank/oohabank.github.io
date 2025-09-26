<?php

namespace App\Views;

use App\Bootstrap;
use App\Models\Payments\Payments;
use App\Models\Users\Users;
use App\Models\Servers\Servers;

class Admin {
	public function index() {
		if(!Users::currentAuth()){ Bootstrap::redirect('/?login/'); }

		echo Bootstrap::templaterEnv()->render('Resources/Admin/index.tpl', [
			'users' => Users::Listing()->getCount(),
			'servers' => Servers::Listing()->getCount(),
			'payments' => Payments::Listing()->getCount(),
			'successpayments' => Payments::Listing()->setStatus(1)->getCount(),
		]);
	}
}

?>