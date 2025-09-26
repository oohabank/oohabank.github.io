<?php

namespace App\Views\Admin;

use App\Bootstrap;
use App\Models\Users\Users;
use App\Models\Servers\Servers as S;

class Servers {
	public function index() {
		if(!Users::currentAuth()){ Bootstrap::redirect(); }

		$listing = S::Listing()->setPagination();

		echo Bootstrap::templaterEnv()->render('Resources/Admin/Servers/index.tpl', [
			'servers' => $listing->get(),
			'servers_num' => $listing->getCount(),
			'pagination' => $listing->getPagination()
		]);
	}

	public function add() {
		if(!Users::currentAuth()){ Bootstrap::redirect(); }

		echo Bootstrap::templaterEnv()->render('Resources/Admin/Servers/Add/index.tpl');
	}

	public function addPost() {

		Bootstrap::runCSRF(@$_POST['token']);

		if(!Users::currentAuth()){
			Bootstrap::response('У Вас недостаточно прав для доступа к вабранному разделу сайта', 'Ошибка доступа!');
		}

		$_POST = array_map('trim', $_POST);

		$title = isset($_POST['title']) ? $_POST['title'] : '';
		$text = isset($_POST['text']) ? $_POST['text'] : '';
		$rcon_host = isset($_POST['rcon_host']) ? $_POST['rcon_host'] : '';
		$rcon_password = isset($_POST['rcon_password']) ? $_POST['rcon_password'] : '';
		$rcon_port = intval(@$_POST['rcon_port']);

		if(empty($title) || mb_strlen($title, 'UTF-8') > 32){
			Bootstrap::response('Название сервера заполнено неверно', 'Внимание!');
		}

		if(!preg_match('/^[a-z0-9\.\-]+$/i', $rcon_host)){
			Bootstrap::response('Неверный формат адреса RCON сервера', 'Внимание!');
		}

		$server = S::Server()->setRconHost($rcon_host)->setRconPort($rcon_port);

		if($server->get()){
			Bootstrap::response('Сервер с указанным адресом и портом уже существует', 'Ошибка!');
		}

		$server->setRconPassword($rcon_password)
			->setTitle($title)
			->setText($text);

		if(!$server->insert()){
			Bootstrap::response('Произошла ошибка добавления сервера. Обратитесь к администрации', 'Внимание!');
		}

		Bootstrap::response('Сервер успешно добавлен', 'Поздравляем!', true, [
			'id' => $server->getID()
		]);
	}

	public function edit() {
		if(!Users::currentAuth()){ Bootstrap::redirect(); }

		$server_id = intval(@$_GET['edit']);

		$server = S::Server()->setID($server_id);

		if(!$server->get()){
			Bootstrap::redirect('/?admin/servers/');
		}

		echo Bootstrap::templaterEnv()->render('Resources/Admin/Servers/Edit/index.tpl', [
			'server' => $server,
		]);
	}

	public function editPost() {

		Bootstrap::runCSRF(@$_POST['token']);

		if(!Users::currentAuth()){
			Bootstrap::response('У Вас недостаточно прав для доступа к вабранному разделу сайта', 'Ошибка доступа!');
		}

		$server = S::Server()->setID(intval(@$_POST['id']));

		if(!$server->get()){
			Bootstrap::response('Выбранный элемент недоступен', 'Ошибка!');
		}

		$_POST = array_map('trim', $_POST);

		$title = isset($_POST['title']) ? $_POST['title'] : '';
		$text = isset($_POST['text']) ? $_POST['text'] : '';
		$rcon_host = isset($_POST['rcon_host']) ? $_POST['rcon_host'] : '';
		$rcon_password = isset($_POST['rcon_password']) ? $_POST['rcon_password'] : '';
		$rcon_port = intval(@$_POST['rcon_port']);

		if(empty($title) || mb_strlen($title, 'UTF-8') > 32){
			Bootstrap::response('Название сервера заполнено неверно', 'Внимание!');
		}

		if(!preg_match('/^[a-z0-9\.\-]+$/i', $rcon_host)){
			Bootstrap::response('Неверный формат адреса RCON сервера', 'Внимание!');
		}

		if($server->getRconHost() != $rcon_host || $server->getRconPort() != $rcon_port){
			if(S::Server()->setRconHost($rcon_host)->setRconPort($rcon_port)->get()){
				Bootstrap::response('Адрес RCON сервера уже используется', 'Ошибка!');
			}
		}

		$server->setTitle($title)
			->setText($text)
			->setRconHost($rcon_host)
			->setRconPort($rcon_port)
			->setRconPassword($rcon_password);

		if(!$server->update()){
			Bootstrap::response('Произошла ошибка сохранения сервера. Обратитесь к администрации', 'Внимание!');
		}

		Bootstrap::response('Сервер успешно сохранен', 'Поздравляем!', true);
	}

	public function remove() {

		Bootstrap::runCSRF(@$_POST['token']);

		if(!Users::currentAuth()){
			Bootstrap::response('У Вас недостаточно прав для доступа к вабранному разделу сайта', 'Ошибка доступа!');
		}

		$server = S::Server()->setID(intval(@$_GET['remove']));

		if(!$server->get()){
			Bootstrap::response('Выбранный элемент недоступен', 'Ошибка!');
		}

		if(!$server->delete()){
			Bootstrap::response('Произошла ошибка удаления сервера. Обратитесь к администрации', 'Внимание!');
		}

		Bootstrap::response('Сервер успешно удален', 'Поздравляем!', true);
	}
}

?>