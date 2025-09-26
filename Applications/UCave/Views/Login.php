<?php

namespace App\Views;

use App\Bootstrap;
use App\Models\Users\Users;

class Login {

	public function page() {

		if(Users::currentAuth()){ Bootstrap::redirect('/?admin/'); }

		echo Bootstrap::templaterEnv()->render('Resources/Login/index.tpl');
	}

	public function post() {
		$login = @$_POST['login'];
		$password = @$_POST['password'];

		Bootstrap::runCSRF(@$_POST['token']);

		if(empty($login) || empty($password)){
			Bootstrap::response('Необходимо заполнить форму авторизации', 'Внимание!');
		}

		if(Users::currentAuth()){
			Bootstrap::response('Вы уже авторизованы на сайте. Попробуйте обновить страницу', 'Ошибка доступа!');
		}

		$user = Users::User();

		if(preg_match('/^[a-z0-9\-_\.]+@[a-z0-9\-\.]+$/i', $login)){
			$user->setEmail($login);
		}else{
			$user->setLogin($login);
		}

		if(!$user->get()){
			Bootstrap::response('Неверный логин/E-Mail или пароль', 'Ошибка!');
		}

		if(!Users::checkPassword($password, $user->getPassword())){
			Bootstrap::response('Неверный логин/E-Mail или пароль', 'Ошибка!');
		}

		$auth = Users::Auth()
			->setToken(md5(mt_rand(9999999, 999999999)))
			->setUserID($user->getID())
			->setDateExpire(time()+(86400*365))
			->setIP(Users::IP());

		$auth->insert();

		$auth->save();

		Bootstrap::response('Вы успешно авторизовались в панели управления', 'Поздравляем!', true);
	}

	public function logout() {

		$auth = Users::currentAuth();

		if(!$auth){
			Bootstrap::redirect();
		}

		Users::current()->setDateUpdate(time())->update();

		$auth->delete();

		$auth->unsave();

		Bootstrap::redirect();
	}
}

?>