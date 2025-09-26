<?php

namespace App\Views\Admin;

use App\Bootstrap;
use App\Models\Users\Users;
use App\Models\Promo\Promo as P;

class Promo {
	public function index() {
		if(!Users::currentAuth()){ Bootstrap::redirect(); }

		$listing = P::Listing()->setPagination();

		echo Bootstrap::templaterEnv()->render('Resources/Admin/Promo/index.tpl', [
			'codes' => $listing->get(),
			'codes_num' => $listing->getCount(),
			'pagination' => $listing->pagination()
		]);
	}

	public function add() {
		if(!Users::currentAuth()){ Bootstrap::redirect(); }

		echo Bootstrap::templaterEnv()->render('Resources/Admin/Promo/Add/index.tpl');
	}

	public function edit() {
		if(!Users::currentAuth()){ Bootstrap::redirect(); }

		$code = P::Code()->setID(intval(@$_GET['edit']));

		if(!$code->get()){ Bootstrap::redirect('/?admin/promo/'); }

		echo Bootstrap::templaterEnv()->render('Resources/Admin/Promo/Edit/index.tpl', [
			'code' => $code
		]);
	}

	public function addPost() {

		Bootstrap::runCSRF(@$_POST['token']);

		if(!Users::currentAuth()){
			Bootstrap::response('У Вас недостаточно прав для доступа к вабранному разделу сайта', 'Ошибка доступа!');
		}

		$key = isset($_POST['code']) ? $_POST['code'] : '';
		$discount = floatval(@$_POST['discount']);
		$generate = intval(@$_POST['generate']);

		if(!$generate && empty($key)){
			Bootstrap::response('Не заполнено поле кода', 'Внимание!');
		}

		if($generate){
			$key = substr(strtoupper(md5(mt_rand(0,99999999))), 0, 10);
		}

		if($discount < 0){
			Bootstrap::response('Размер скидки должен быть не меньше нуля', 'Внимание!');
		}

		$code = P::Code()->setCode($key);

		if($code->get()){
			Bootstrap::response('Указанный код промо-кода уже занят', 'Внимание!');
		}

		$code->setDiscount($discount);

		if(!$code->insert()){
			Bootstrap::response('Произошла ошибка добавления промо-кода. Обратитесь к администрации', 'Внимание!');
		}

		Bootstrap::response('Промо-код успешно добавлен', 'Поздравляем!', true);
	}

	public function editPost() {

		Bootstrap::runCSRF(@$_POST['token']);

		if(!Users::currentAuth()){
			Bootstrap::response('У Вас недостаточно прав для доступа к вабранному разделу сайта', 'Ошибка доступа!');
		}

		$code = P::Code()->setID(intval(@$_POST['id']));

		if(!$code->get()){
			Bootstrap::response('Выбранный элемент недоступен', 'Ошибка!');
		}

		$key = isset($_POST['code']) ? $_POST['code'] : '';
		$discount = floatval(@$_POST['discount']);
		$generate = intval(@$_POST['generate']);

		if(!$generate && empty($key)){
			Bootstrap::response('Не заполнено поле кода', 'Внимание!');
		}

		if($generate){
			$key = substr(strtoupper(md5(mt_rand(0,99999999))), 0, 10);
		}

		if($discount < 0){
			Bootstrap::response('Размер скидки должен быть не меньше нуля', 'Внимание!');
		}

		if($code->getCode() != $key && P::Code()->setCode($key)->get()){
			Bootstrap::response('Указанный код промо-кода уже занят', 'Внимание!');
		}

		$code->setCode($key)->setDiscount($discount)->setDateUpdate(time())->setUpdaterID(null);

		if(!$code->update()){
			Bootstrap::response('Произошла ошибка сохранения промо-кода. Обратитесь к администрации', 'Внимание!');
		}

		Bootstrap::response('Промо-код успешно сохранен', 'Поздравляем!', true);
	}

	public function remove() {

		Bootstrap::runCSRF(@$_POST['token']);

		if(!Users::currentAuth()){
			Bootstrap::response('У Вас недостаточно прав для доступа к вабранному разделу сайта', 'Ошибка доступа!');
		}

		$code = P::Code()->setID(intval(@$_GET['remove']));

		if(!$code->get()){
			Bootstrap::response('Выбранный элемент недоступен', 'Ошибка!');
		}

		if(!$code->delete()){
			Bootstrap::response('Произошла ошибка удаления промо-кода. Обратитесь к администрации', 'Внимание!');
		}

		Bootstrap::response('Промо-код успешно удален', 'Поздравляем!', true);
	}
}

?>