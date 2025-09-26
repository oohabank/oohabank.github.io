<?php

namespace App\Views\Admin;

use App\Bootstrap;
use App\Models\Users\Users;
use App\Models\Statics\Statics as S;

class Statics {
	public function index() {
		if(!Users::currentAuth()){ Bootstrap::redirect(); }

		$listing = S::Listing()->setSort("`s`.`id` DESC")->setPagination();

		echo Bootstrap::templaterEnv()->render('Resources/Admin/Statics/index.tpl', [
			'statics' => $listing->get(),
			'statics_num' => $listing->getCount(),
			'pagination' => $listing->pagination()
		]);
	}

	public function add() {
		if(!Users::currentAuth()){ Bootstrap::redirect(); }

		echo Bootstrap::templaterEnv()->render('Resources/Admin/Statics/Add/index.tpl', [
			'template' => file_get_contents(__ROOT__.'/Public/Themes/Default/Resources/Admin/Statics/template.tpl')
		]);
	}

	public function addPost() {

		Bootstrap::runCSRF(@$_POST['token']);

		if(!Users::currentAuth()){
			Bootstrap::response('У Вас недостаточно прав для доступа к вабранному разделу сайта', 'Ошибка доступа!');
		}

		$title = isset($_POST['title']) ? trim($_POST['title']) : '';
		$name = isset($_POST['name']) ? trim($_POST['name']) : '';
		$content = isset($_POST['content']) ? trim($_POST['content']) : '';

		if(empty($title)){
			Bootstrap::response('Не заполнено название страницы', 'Внимание!');
		}

		$name = empty($name) ? Bootstrap::toLatin($title) : Bootstrap::toLatin($name);

		$page = S::Page()->setName($name);

		if($page->get()){
			Bootstrap::response('Страница с указанным названием уже существует', 'Ошибка!');
		}

		$page->setTitle($title)->setContent($content);

		if(!$page->insert()){
			Bootstrap::response('Произошла ошибка добавления страницы. Обратитесь к администрации', 'Внимание!');
		}

		Bootstrap::response('Страница успешно добавлена', 'Поздравляем!', true);
	}

	public function edit() {
		if(!Users::currentAuth()){ Bootstrap::redirect(); }

		$page = S::Page()->setID(intval(@$_GET['edit']));

		if(!$page->get()){
			Bootstrap::redirect('/?admin/statics/');
		}

		echo Bootstrap::templaterEnv()->render('Resources/Admin/Statics/Edit/index.tpl', [
			'page' => $page
		]);
	}

	public function editPost() {

		Bootstrap::runCSRF(@$_POST['token']);

		if(!Users::currentAuth()){
			Bootstrap::response('У Вас недостаточно прав для доступа к вабранному разделу сайта', 'Ошибка доступа!');
		}

		$page = S::Page()->setID(intval(@$_POST['id']));

		if(!$page->get()){
			Bootstrap::response('Выбранный элемент недоступен', 'Ошибка!');
		}

		$title = isset($_POST['title']) ? trim($_POST['title']) : '';
		$name = isset($_POST['name']) ? trim($_POST['name']) : '';
		$content = isset($_POST['content']) ? trim($_POST['content']) : '';

		if(empty($title)){
			Bootstrap::response('Не заполнено название страницы', 'Внимание!');
		}

		$name = empty($name) ? Bootstrap::toLatin($title) : Bootstrap::toLatin($name);

		if($page->getName() != $name && S::Page()->setName($name)->get()){
			Bootstrap::response('Страница с указанным названием уже существует', 'Ошибка!');
		}

		$page->setName($name)->setTitle($title)->setContent($content)
			->setDateUpdate(time())->setUpdaterID(null);

		if(!$page->update()){
			Bootstrap::response('Произошла ошибка сохранения страницы. Обратитесь к администрации', 'Внимание!');
		}

		Bootstrap::response('Страница успешно сохранена', 'Поздравляем!', true);
	}

	public function remove() {

		Bootstrap::runCSRF(@$_POST['token']);

		if(!Users::currentAuth()){
			Bootstrap::response('У Вас недостаточно прав для доступа к вабранному разделу сайта', 'Ошибка доступа!');
		}

		$page = S::Page()->setID(intval(@$_GET['remove']));

		if(!$page->get()){
			Bootstrap::response('Выбранный элемент недоступен', 'Ошибка!');
		}

		if(!$page->delete()){
			Bootstrap::response('Произошла ошибка удаления страницы. Обратитесь к администрации', 'Внимание!');
		}

		Bootstrap::response('Страница успешно удалена', 'Поздравляем!', true);
	}
}

?>