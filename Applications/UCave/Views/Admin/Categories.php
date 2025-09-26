<?php

namespace App\Views\Admin;

use App\Bootstrap;
use App\Models\Users\Users;
use App\Models\Categories\Categories as C;
use App\Models\Sections\Sections;

class Categories {
	public function index() {
		if(!Users::currentAuth()){ Bootstrap::redirect(); }

		$listing = C::Listing()->setPagination();

		echo Bootstrap::templaterEnv()->render('Resources/Admin/Categories/index.tpl', [
			'categories' => $listing->get(),
			'categories_num' => $listing->getCount(),
			'pagination' => $listing->getPagination()
		]);
	}

	public function add() {
		if(!Users::currentAuth()){ Bootstrap::redirect(); }

		echo Bootstrap::templaterEnv()->render('Resources/Admin/Categories/Add/index.tpl', [
			'sections' => Sections::Listing()->get(),
		]);
	}

	public function edit() {
		if(!Users::currentAuth()){ Bootstrap::redirect(); }

		$category_id = intval(@$_GET['edit']);

		$category = C::Category()->setID($category_id);

		if(!$category->get()){
			Bootstrap::redirect('/?admin/categories/');
		}

		echo Bootstrap::templaterEnv()->render('Resources/Admin/Categories/Edit/index.tpl', [
			'sections' => Sections::Listing()->get(),
			'category' => $category
		]);
	}

	public function addPost() {

		Bootstrap::runCSRF(@$_POST['token']);

		if(!Users::currentAuth()){
			Bootstrap::response('У Вас недостаточно прав для доступа к вабранному разделу сайта', 'Ошибка доступа!');
		}

		$title = isset($_POST['title']) ? $_POST['title'] : '';
		$text = isset($_POST['text']) ? $_POST['text'] : '';
		$section_id = intval(@$_POST['section_id']);

		if(empty($title)){
			Bootstrap::response('Не заполнено название подкатегории', 'Внимание!');
		}

		if(!Sections::Section()->setID($section_id)->get()){
			Bootstrap::response('Категория указана неверно', 'Ошибка!');
		}

		$category = C::Category()->setTitle($title)->setText($text)->setSectionID($section_id);

		if(!$category->insert()){
			Bootstrap::response('Произошла ошибка добавления подкатегории. Обратитесь к администрации', 'Внимание!');
		}

		Bootstrap::response('Подкатегория успешно добавлена', 'Поздравляем!', true);
	}

	public function editPost() {

		Bootstrap::runCSRF(@$_POST['token']);

		if(!Users::currentAuth()){
			Bootstrap::response('У Вас недостаточно прав для доступа к вабранному разделу сайта', 'Ошибка доступа!');
		}

		$category = C::Category()->setID(intval(@$_POST['id']));

		if(!$category->get()){
			Bootstrap::response('Выбранный элемент недоступен', 'Ошибка!');
		}

		$title = isset($_POST['title']) ? $_POST['title'] : '';
		$text = isset($_POST['text']) ? $_POST['text'] : '';
		$section_id = intval(@$_POST['section_id']);

		if(empty($title)){
			Bootstrap::response('Не заполнено название подкатегории', 'Внимание!');
		}

		if(!Sections::Section()->setID($section_id)->get()){
			Bootstrap::response('Категория указана неверно', 'Ошибка!');
		}

		$category->setTitle($title)->setText($text)->setSectionID($section_id)->setDateUpdate(time())->setUpdaterID(null);

		if(!$category->update()){
			Bootstrap::response('Произошла ошибка сохранения подкатегории. Обратитесь к администрации', 'Внимание!');
		}

		Bootstrap::response('Подкатегория успешно сохранена', 'Поздравляем!', true);
	}

	public function remove() {

		Bootstrap::runCSRF(@$_POST['token']);

		if(!Users::currentAuth()){
			Bootstrap::response('У Вас недостаточно прав для доступа к вабранному разделу сайта', 'Ошибка доступа!');
		}

		$category = C::Category()->setID(intval(@$_GET['remove']));

		if(!$category->get()){
			Bootstrap::response('Выбранный элемент недоступен', 'Ошибка!');
		}

		if(!$category->delete()){
			Bootstrap::response('Произошла ошибка удаления подкатегории. Обратитесь к администрации', 'Внимание!');
		}

		Bootstrap::response('Подкатегория успешно удалена', 'Поздравляем!', true);
	}
}

?>