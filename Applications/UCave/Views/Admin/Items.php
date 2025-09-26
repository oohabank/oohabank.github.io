<?php

namespace App\Views\Admin;

use App\Bootstrap;
use App\Models\Users\Users;
use App\Models\Items\Items as I;
use App\Models\Sections\Sections;
use App\Models\Servers\Servers;
use App\Models\Categories\Categories;

class Items {
	public function index() {
		if(!Users::currentAuth()){ Bootstrap::redirect(); }

		$listing = I::Listing()->setPagination();

		echo Bootstrap::templaterEnv()->render('Resources/Admin/Items/index.tpl', [
			'items' => $listing->get(),
			'items_num' => $listing->getCount(),
			'pagination' => $listing->getPagination()
		]);
	}

	public function add() {
		if(!Users::currentAuth()){ Bootstrap::redirect(); }

		echo Bootstrap::templaterEnv()->render('Resources/Admin/Items/Add/index.tpl', [
			'sections' => Sections::Listing()->get(),
			'servers' => Servers::Listing()->get(),
			'categories' => Categories::Listing()->get(),
		]);
	}

	public function addPost() {

		Bootstrap::runCSRF(@$_POST['token']);

		if(!Users::currentAuth()){
			Bootstrap::response('У Вас недостаточно прав для доступа к вабранному разделу сайта', 'Ошибка доступа!');
		}

		$title = isset($_POST['title']) ? $_POST['title'] : '';
		$name = isset($_POST['name']) ? $_POST['name'] : '';
		$text = isset($_POST['text']) ? $_POST['text'] : '';
		$image = isset($_POST['image']) ? $_POST['image'] : '';
		$command = isset($_POST['command']) ? $_POST['command'] : '';
		$section_id = intval(@$_POST['section_id']);
		$server_id = intval(@$_POST['server_id']);
		$category_id = intval(@$_POST['category_id']);
		$prefix = intval(@$_POST['prefix']);
		$prefix_min = intval(@$_POST['prefix_min']);
		$prefix_max = intval(@$_POST['prefix_max']);
		$surcharge = intval(@$_POST['surcharge']);
		$price = floatval(@$_POST['price']);
		$surcharge = $surcharge > 0 ? 1 : 0;
		$prefix = $prefix > 0 ? 1 : 0;
		$prefix_min = $prefix_min < 1 ? 1 :$prefix_min;
		$prefix_max = $prefix_max < 1 ? 1 :$prefix_max;

		if($prefix && $prefix_min > $prefix_max){
			Bootstrap::response('Минимальная длина префикса не может быть больше максимально', 'Ошибка!');
		}

		if(empty($title)){
			Bootstrap::response('Не заполнено название товара', 'Внимание!');
		}

		if(empty($command)){
			Bootstrap::response('Необходимо заполнить команду', 'Внимание!');
		}

		if($price < 1){
			Bootstrap::response('Цена не может быть меньше одного рубля', 'Ошибка!');
		}

		if(!$section_id && !$category_id){
			Bootstrap::response('Необходимо выбрать категорию или подкатегорию', 'Ошибка!');
		}

		if(empty($name)){ $name = $title; }

		if(!Servers::Server()->setID($server_id)->get()){
			Bootstrap::response('Сервер указан неверно', 'Ошибка!');
		}

		if($category_id){
			$category = Categories::Category()->setID($category_id);

			if(!$category->get()){
				Bootstrap::response('Подкатегория указана неверно', 'Ошибка!');
			}

			$section_id = $category->getSectionID();
		}

		if(!Sections::Section()->setID($section_id)->get()){
			Bootstrap::response('Категория в выбранной подкатегории недоступна', 'Ошибка!');
		}

		$item = I::Item()
			->setTitle($title)
			->setName(Bootstrap::toLatin($name))
			->setText($text)
			->setImage($image)
			->setPrice($price)
			->setSectionID($section_id)
			->setCategoryID($category_id)
			->setServerID($server_id)
			->setSurcharge($surcharge)
			->setPrefix($prefix)
			->setPrefixMin($prefix_min)
			->setPrefixMax($prefix_max)
			->setCommand($command);

		if(!$item->insert()){
			Bootstrap::response('Произошла ошибка добавления товара. Обратитесь к администрации', 'Внимание!');
		}

		Bootstrap::response('Товар успешно добавлен', 'Поздравляем!', true);
	}

	public function edit() {
		if(!Users::currentAuth()){ Bootstrap::redirect(); }

		$item = I::Item()->setID(intval(@$_GET['edit']));

		if(!$item->get()){
			Bootstrap::redirect('/?admin/categories/');
		}

		echo Bootstrap::templaterEnv()->render('Resources/Admin/Items/Edit/index.tpl', [
			'sections' => Sections::Listing()->get(),
			'servers' => Servers::Listing()->get(),
			'categories' => Categories::Listing()->get(),
			'item' => $item
		]);
	}

	public function editPost() {

		Bootstrap::runCSRF(@$_POST['token']);

		if(!Users::currentAuth()){
			Bootstrap::response('У Вас недостаточно прав для доступа к вабранному разделу сайта', 'Ошибка доступа!');
		}

		$item = I::Item()->setID(intval(@$_POST['id']));

		if(!$item->get()){
			Bootstrap::response('Выбранный элемент недоступен', 'Ошибка!');
		}

		$title = isset($_POST['title']) ? $_POST['title'] : '';
		$name = isset($_POST['name']) ? $_POST['name'] : '';
		$text = isset($_POST['text']) ? $_POST['text'] : '';
		$image = isset($_POST['image']) ? $_POST['image'] : '';
		$command = isset($_POST['command']) ? $_POST['command'] : '';
		$section_id = intval(@$_POST['section_id']);
		$server_id = intval(@$_POST['server_id']);
		$category_id = intval(@$_POST['category_id']);
		$prefix = intval(@$_POST['prefix']);
		$prefix_min = intval(@$_POST['prefix_min']);
		$prefix_max = intval(@$_POST['prefix_max']);
		$surcharge = intval(@$_POST['surcharge']);
		$price = floatval(@$_POST['price']);
		$surcharge = $surcharge > 0 ? 1 : 0;
		$prefix = $prefix > 0 ? 1 : 0;

		if($prefix && ($prefix_min < 1 || $prefix_max < 1)){
			Bootstrap::response('Длина префикса не может быть меньше 1', 'Ошибка!');
		}

		if($prefix && $prefix_min > $prefix_max){
			Bootstrap::response('Минимальная длина префикса не может быть больше максимально', 'Ошибка!');
		}

		if(empty($title)){
			Bootstrap::response('Не заполнено название товара', 'Внимание!');
		}

		if(empty($command)){
			Bootstrap::response('Необходимо заполнить команду', 'Внимание!');
		}

		if($price < 1){
			Bootstrap::response('Цена не может быть меньше одного рубля', 'Ошибка!');
		}

		if(!$section_id && !$category_id){
			Bootstrap::response('Необходимо выбрать категорию или подкатегорию', 'Ошибка!');
		}

		if(empty($name)){ $name = $title; }

		if($name != $item->getName() && I::Item()->setName($name)->get()){
			Bootstrap::response('Уникальное имя уже используется', 'Ошибка!');
		}

		if(!Servers::Server()->setID($server_id)->get()){
			Bootstrap::response('Сервер указан неверно', 'Ошибка!');
		}

		if($category_id){
			$category = Categories::Category()->setID($category_id);

			if(!$category->get()){
				Bootstrap::response('Подкатегория указана неверно', 'Ошибка!');
			}

			$section_id = $category->getSectionID();
		}

		if(!Sections::Section()->setID($section_id)->get()){
			Bootstrap::response('Категория в выбранной подкатегории недоступна', 'Ошибка!');
		}

		$item->setTitle($title)
			->setText($text)
			->setImage($image)
			->setName(Bootstrap::toLatin($name))
			->setPrice($price)
			->setSectionID($section_id)
			->setCategoryID($category_id)
			->setServerID($server_id)
			->setCommand($command)
			->setSurcharge($surcharge)
			->setPrefix($prefix)
			->setPrefixMin($prefix_min)
			->setPrefixMax($prefix_max)
			->setDateUpdate(time())->setUpdaterID(null);

		if(!$item->update()){
			Bootstrap::response('Произошла ошибка сохранения товара. Обратитесь к администрации', 'Внимание!');
		}

		Bootstrap::response('Товар успешно сохранен', 'Поздравляем!', true);
	}

	public function remove() {

		Bootstrap::runCSRF(@$_POST['token']);

		if(!Users::currentAuth()){
			Bootstrap::response('У Вас недостаточно прав для доступа к вабранному разделу сайта', 'Ошибка доступа!');
		}

		$item = I::Item()->setID(intval(@$_GET['remove']));

		if(!$item->get()){
			Bootstrap::response('Выбранный элемент недоступен', 'Ошибка!');
		}

		if(!$item->delete()){
			Bootstrap::response('Произошла ошибка удаления товара. Обратитесь к администрации', 'Внимание!');
		}

		Bootstrap::response('Товар успешно удален', 'Поздравляем!', true);
	}
}

?>