<?php

namespace App\Views\Admin;

use App\Bootstrap;
use App\Models\Users\Users as U;

class Users {
	public function index() {
		if(!U::currentAuth()){ Bootstrap::redirect(); }

		$listing = U::Listing()->setPagination();

		echo Bootstrap::templaterEnv()->render('Resources/Admin/Users/index.tpl', [
			'users' => $listing->get(),
			'users_num' => $listing->getCount(),
			'pagination' => $listing->pagination(),
		]);
	}

	public function add() {
		if(!U::currentAuth()){ Bootstrap::redirect(); }

		echo Bootstrap::templaterEnv()->render('Resources/Admin/Users/Add/index.tpl');
	}

	public function addPost() {

		Bootstrap::runCSRF(@$_POST['token']);

		if(!U::currentAuth()){
			Bootstrap::response('У Вас недостаточно прав для доступа к вабранному разделу сайта', 'Ошибка доступа!');
		}

		$login = isset($_POST['login']) ? $_POST['login'] : '';
		$email = isset($_POST['email']) ? $_POST['email'] : '';
		$password = isset($_POST['password']) ? $_POST['password'] : '';

		if(empty($login) || !preg_match('/^[a-z0-9_\.\-]+$/i', $login)){
			Bootstrap::response('Логин пользователя указан неверно', 'Внимание!');
		}

		if(empty($email) || !preg_match('/^[a-z0-9_\.\-]+\@[a-z0-9_\.\-]+$/i', $email)){
			Bootstrap::response('E-Mail пользователя указан неверно', 'Внимание!');
		}

		if(empty($password) || mb_strlen($password, 'UTF-8') < 6){
			Bootstrap::response('Пароль должен быть не короче 6 символов', 'Внимание!');
		}

		if(U::User()->setLogin($login)->get()){
			Bootstrap::response('Выбранный логин уже занят', 'Ошибка!');
		}

		if(U::User()->setEmail($email)->get()){
			Bootstrap::response('Выбранный E-Mail адрес уже занят', 'Ошибка!');
		}

		$u = U::User()->setLogin($login)->setEmail($email)->setPassword(U::createPassword($password));

		if(!$u->insert()){
			Bootstrap::response('Произошла ошибка добавления пользователя. Обратитесь к администрации', 'Внимание!');
		}

		Bootstrap::response('Пользователь успешно добавлен', 'Поздравляем!', true);
	}

	public function edit() {

		if(!U::currentAuth()){
			Bootstrap::response('У Вас недостаточно прав для доступа к вабранному разделу сайта', 'Ошибка доступа!');
		}

		$u = U::User()->setID(intval(@$_GET['edit']));

		if(!$u->get()){
			Bootstrap::redirect('/?admin/users/');
		}

		echo Bootstrap::templaterEnv()->render('Resources/Admin/Users/Edit/index.tpl', [
			'user' => $u
		]);
	}

	public function editPost() {

		Bootstrap::runCSRF(@$_POST['token']);

		if(!U::currentAuth()){
			Bootstrap::response('У Вас недостаточно прав для доступа к вабранному разделу сайта', 'Ошибка доступа!');
		}

		$u = U::User()->setID(intval(@$_POST['id']));

		if(!$u->get()){
			Bootstrap::response('Выбранный элемент недоступен', 'Ошибка!');
		}

		$login = isset($_POST['login']) ? $_POST['login'] : '';
		$email = isset($_POST['email']) ? $_POST['email'] : '';
		$password = isset($_POST['password']) ? $_POST['password'] : '';

		if(empty($login) || !preg_match('/^[a-z0-9_\.\-]+$/i', $login)){
			Bootstrap::response('Логин пользователя указан неверно', 'Внимание!');
		}

		if(empty($email) || !preg_match('/^[a-z0-9_\.\-]+\@[a-z0-9_\.\-]+$/i', $email)){
			Bootstrap::response('E-Mail пользователя указан неверно', 'Внимание!');
		}

		if(!empty($password)){
			if(mb_strlen($password, 'UTF-8') < 6){
				Bootstrap::response('Пароль должен быть не короче 6 символов', 'Внимание!');
			}

			$u->setPassword(U::createPassword($password));
		}

		if($u->getLogin() != $login && U::User()->setLogin($login)->get()){
			Bootstrap::response('Выбранный логин уже занят', 'Ошибка!');
		}

		if($u->getEmail() != $email && U::User()->setEmail($email)->get()){
			Bootstrap::response('Выбранный E-Mail адрес уже занят', 'Ошибка!');
		}

		$u->setLogin($login)->setEmail($email);

		if(!$u->update()){
			Bootstrap::response('Произошла ошибка сохранения пользователя. Обратитесь к администрации', 'Внимание!');
		}

		Bootstrap::response('Пользователь успешно сохранен', 'Поздравляем!', true);
	}

	public function remove() {

		Bootstrap::runCSRF(@$_POST['token']);

		if(!U::currentAuth()){
			Bootstrap::response('У Вас недостаточно прав для доступа к вабранному разделу сайта', 'Ошибка доступа!');
		}

		$u = U::User()->setID(intval(@$_GET['remove']));

		if(!$u->get()){
			Bootstrap::response('Выбранный элемент недоступен', 'Ошибка!');
		}

		if(!$u->delete()){
			Bootstrap::response('Произошла ошибка удаления пользователя. Обратитесь к администрации', 'Внимание!');
		}

		Bootstrap::response('Пользователь успешно удален', 'Поздравляем!', true);
	}
}

?>