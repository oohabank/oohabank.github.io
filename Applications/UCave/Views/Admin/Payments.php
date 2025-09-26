<?php

namespace App\Views\Admin;

use App\Bootstrap;
use App\Models\Users\Users;
use App\Models\Payments\Payments as P;

class Payments {
	public function index() {
		if(!Users::currentAuth()){ Bootstrap::redirect(); }

		$listing = P::Listing()->setPagination();

		echo Bootstrap::templaterEnv()->render('Resources/Admin/Payments/index.tpl', [
			'payments' => $listing->get(),
			'payments_num' => $listing->getCount(),
			'pagination' => $listing->getPagination()
		]);
	}

	public function remove() {

		Bootstrap::runCSRF(@$_POST['token']);

		if(!Users::currentAuth()){
			Bootstrap::response('У Вас недостаточно прав для доступа к вабранному разделу сайта', 'Ошибка доступа!');
		}

		$payment = P::Payment()->setID(intval(@$_GET['remove']));

		if(!$payment->get()){
			Bootstrap::response('Выбранный элемент недоступен', 'Ошибка!');
		}

		if(!$payment->delete()){
			Bootstrap::response('Произошла ошибка удаления платежа. Обратитесь к администрации', 'Внимание!');
		}

		Bootstrap::response('Платеж успешно удален', 'Поздравляем!', true);
	}
}

?>