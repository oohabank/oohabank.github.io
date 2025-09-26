<?php

namespace App\Views;

use App\Bootstrap;
use App\Models\Monitoring as M;

class Monitoring {

	public function post() {
		Bootstrap::runCSRF(@$_POST['token']);

		$m = new M();

		Bootstrap::response('OK', 'OK', true, $m->getUpdatedData());
	}
}

?>