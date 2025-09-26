<?php

namespace App\Views\Admin;

use App\Bootstrap;
use App\Models\Users\Users;
use App\Models\Sections\Sections as S;

class Sections {
	public function index() {
		if(!Users::currentAuth()){ Bootstrap::redirect(); }

		$listing = S::Listing()->setSort("`s`.`id` DESC")->setPagination();

		echo Bootstrap::templaterEnv()->render('Resources/Admin/Sections/index.tpl', [
			'sections' => $listing->get(),
			'sections_num' => $listing->getCount(),
			'pagination' => $listing->getPagination()
		]);
	}

	public function add() {
		if(!Users::currentAuth()){ Bootstrap::redirect(); }

		echo Bootstrap::templaterEnv()->render('Resources/Admin/Sections/Add/index.tpl');
	}

	public function edit() {
		if(!Users::currentAuth()){ Bootstrap::redirect(); }

		$section_id = intval(@$_GET['edit']);

		$section = S::Section()->setID($section_id);

		if(!$section->get()){
			Bootstrap::redirect('/?admin/sections/');
		}

		echo Bootstrap::templaterEnv()->render('Resources/Admin/Sections/Edit/index.tpl', [
			'section' => $section
		]);
	}

	public function addPost() {

		Bootstrap::runCSRF(@$_POST['token']);

		if(!Users::currentAuth()){
			Bootstrap::response('У Вас недостаточно прав для доступа к вабранному разделу сайта', 'Ошибка доступа!');
		}

		$title = isset($_POST['title']) ? $_POST['title'] : '';
		$text = isset($_POST['text']) ? $_POST['text'] : '';

		if(empty($title)){
			Bootstrap::response('Не заполнено название категории', 'Внимание!');
		}

		$section = S::Section()->setTitle($title)->setText($text);

		if(!$section->insert()){
			Bootstrap::response('Произошла ошибка добавления категории. Обратитесь к администрации', 'Внимание!');
		}

		Bootstrap::response('Категория успешно добавлена', 'Поздравляем!', true);
	}

	public function editPost() {

		Bootstrap::runCSRF(@$_POST['token']);

		if(!Users::currentAuth()){
			Bootstrap::response('У Вас недостаточно прав для доступа к вабранному разделу сайта', 'Ошибка доступа!');
		}

		$section = S::Section()->setID(intval(@$_POST['id']));

		if(!$section->get()){
			Bootstrap::response('Выбранный элемент недоступен', 'Ошибка!');
		}

		$title = isset($_POST['title']) ? $_POST['title'] : '';
		$text = isset($_POST['text']) ? $_POST['text'] : '';

		if(empty($title)){
			Bootstrap::response('Не заполнено название категории', 'Внимание!');
		}

		$section->setTitle($title)->setText($text)->setDateUpdate(time())->setUpdaterID(null);

		if(!$section->update()){
			Bootstrap::response('Произошла ошибка сохранения категории. Обратитесь к администрации', 'Внимание!');
		}

		Bootstrap::response('Категория успешно сохранена', 'Поздравляем!', true);
	}

	public function remove() {

		Bootstrap::runCSRF(@$_POST['token']);

		if(!Users::currentAuth()){
			Bootstrap::response('У Вас недостаточно прав для доступа к вабранному разделу сайта', 'Ошибка доступа!');
		}

		$section = S::Section()->setID(intval(@$_GET['remove']));

		if(!$section->get()){
			Bootstrap::response('Выбранный элемент недоступен', 'Ошибка!');
		}

		if(!$section->delete()){
			Bootstrap::response('Произошла ошибка удаления категории. Обратитесь к администрации', 'Внимание!');
		}

		Bootstrap::response('Категория успешно удалена', 'Поздравляем!', true);
	}
}

?>