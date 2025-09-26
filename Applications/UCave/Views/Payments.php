<?php

namespace App\Views;

use App\Bootstrap;
use App\Models\Items\Items;
use App\Models\Log;
use App\Models\Payments\Payment;
use App\Models\Payments\Payments as P;
use App\Models\Promo\Promo;
use App\Models\Rcon;
use App\Models\Servers\Servers;
use App\Models\Users\Users;
use Component\Database\Database;

class Payments {

	public function index() {
		$user = Users::current();

		if(!$user){ Bootstrap::redirect(); }

		$listing = P::Listing()->setSort("`p`.`id` DESC")->setPagination()->setStatus(1);

		echo Bootstrap::templaterEnv()->render('Resources/Payments/index.tpl', [
			'payments' => $listing->get(),
			'payments_num' => $listing->getCount(),
			'pagination' => $listing->getPagination()
		]);
	}

	private function pay(Payment $payment){

		$select = Database::prepare("SELECT COUNT(1) FROM `users_sum` WHERE `player` = '?'", [$payment->getPlayer()]);

		if($select){
			$ar = $select->fetch_array();

			if(intval($ar[0])){
				Database::prepare("UPDATE `users_sum` SET `sum` = `sum` + {$payment->getSum()} WHERE `player` = '?'", [$payment->getPlayer()]);
			}else{
				Database::prepare("INSERT INTO `users_sum` (`player`, `sum`) VALUES ('?', '?')", [$payment->getPlayer(), $payment->getSum()]);
			}
		}

		$config = Bootstrap::getConfig();

		if($payment->getItemID() > 0){

			$item = Items::Item()->setID($payment->getItemID());

			if($item->get()){

				$server = Servers::Server()->setID($item->getServerID());

				if($server->get()){
					$host = $server->getRconHost();
					$port = $server->getRconPort();
					$password = $server->getRconPassword();
				}

				$command = $item->getCommand();

			}

			$host = !isset($host) ? '127.0.0.1' : $host;
			$port = !isset($port) ? 25575 : $port;
			$password = !isset($password) ? '' : $password;
			$command = !isset($command) ? '' : $command;

		}else{

			$host = $config['balance']['rcon_host'];
			$port = $config['balance']['rcon_port'];
			$password = $config['balance']['rcon_password'];
			$command = $config['balance']['command'];
		}

		$payment->setStatus(1)->update();

		$rcon = new Rcon();

		$rcon->setOptions($host, $port, $password, 5);

		if(!$rcon->connect()){ P::UnitpayResponse('Поздравляем! Счёт успешно оплачен', 1); }

		$item = str_replace('; ', ';', $command);

		$data = @json_decode($payment->getData(), true);

		$prefix = (is_array($data) && isset($data['prefix'])) ? $data['prefix'] : '';

		$item = str_replace(['{PLAYER}', '{AMOUNT}', '{PREFIX}'], [$payment->getPlayer(), floatval($payment->getAmount()), $prefix], $item);

		$items = explode(';', $item);

		$send = false;

		foreach($items as $command){
			$send = $rcon->send_command($command);
		}

		if($send){
			$payment->setDone(1)->update();
		}else{
			Log::warn(var_export($send, true).var_export($command, true));
		}

		P::UnitpayResponse('Поздравляем! Счёт успешно оплачен', 1);
	}

	public function status() {
		$method = @$_REQUEST['method'];

		$params = @$_REQUEST['params'];

		$payment_id = intval(@$params['account']);
		$sum = floatval(@$params['orderSum']);

		$payment = P::Payment()->setID($payment_id)->setSum($sum);

		if(!$payment->get() || ($payment->getStatus() == 1 && $payment->getDone() == 1)){
			P::UnitpayResponse('Счёт не найден');
		}

		if(@$params['signature'] !== P::UnitpaySign($method, @$params)){
			P::UnitpayResponse('Неверная подпись платежа');
		}

		$payment->setResponse(var_export($params, true))->setDateUpdate(time())->update();

		if($method == 'check'){
			P::UnitpayResponse('Проверка прошла успешно', 1);
		}elseif($method == 'pay'){
			$this->pay($payment);
		}else{
			P::UnitpayResponse('Неверный метод платежа');
		}
	}

	public function last() {
		Bootstrap::runCSRF(@$_POST['token']);

		$list = [];

		$config = Bootstrap::getConfig();

		$listing = P::Listing()->setLimit(10)->setStatus(1)->get();

		foreach($listing as $payment){
			$item = $payment->getItem();


			$image = $item->getID() ? $item->getImage() : $config['balance']['image'];

			$list[] = [
				'item_id' => $item->getID(),
				'player' => $payment->getPlayer(),
				'item' => $item->getTitle(),
				'image' => $image,
				'date' => date('d.m.Y в H:i', $payment->getDateUpdate())
			];
		}

		Bootstrap::response('OK', 'OK', true, [
			'list' => $list,
			'hash' => md5(var_export($list, true))
		]);
	}

	public function balancePost() {
		Bootstrap::runCSRF(@$_POST['token']);

		$login = isset($_POST['login']) && is_string($_POST['login']) ? $_POST['login'] : '';
		$amount = floatval(@$_POST['amount']);
		$promo = isset($_POST['promo']) && is_string($_POST['promo']) ? $_POST['promo'] : '';

		if($amount < 1){
			Bootstrap::response('Невернвая сумма платежа', 'Ошибка!');
		}

		if(!Users::exists($login)){
			Bootstrap::response('Профиль не найден', 'Ошибка!');
		}

		$price = $amount;

		$promo_id = 0;

		$promo = Promo::Code()->setCode($promo);

		if($promo->get()){
			if($promo->getDiscount() > 0){
				$price = $price - ($price / 100 * $promo->getDiscount());
			}

			$promo_id = $promo->getID();
		}

		$payment = P::Payment()
			->setPlayer($login)
			->setAmount($amount)
			->setItemID(0)
			->setPromoID($promo_id)
			->setSum($price);

		if(!$payment->insert()){
			Bootstrap::response('Ошибка создания платежа', 'Ошибка!');
		}

		$config = Bootstrap::getConfig();

		$desc = "Пополнение счета игрока {$login} на сумму {$payment->getSum()} Р.";

		$sign = hash('sha256', "{$payment->getID()}{up}{$desc}{up}{$payment->getSum()}{up}{$config['unitpay']['private']}");

		$url = "https://unitpay.{$config['unitpay']['domain']}/pay/{$config['unitpay']['public']}/qiwi?sum={$payment->getSum()}&account={$payment->getID()}&desc={$desc}&signature={$sign}";

		Bootstrap::response('OK', 'OK', true, [
			'url' => $url
		]);
	}

	public function balancePrice() {
		Bootstrap::runCSRF(@$_POST['token']);

		$amount = floatval(@$_POST['amount']);
		$promo = isset($_POST['promo']) && is_string($_POST['promo']) ? $_POST['promo'] : '';

		if($amount < 1){
			Bootstrap::response('Невернвая сумма платежа', 'Ошибка!');
		}

		$price = $amount;

		$promo = Promo::Code()->setCode($promo);

		if($promo->get()){
			if($promo->getDiscount() > 0){
				$price = $price - ($price / 100 * $promo->getDiscount());
			}
		}

		Bootstrap::response('OK', 'OK', true, [
			'price' => $price
		]);
	}

	public function itemPost() {
		Bootstrap::runCSRF(@$_POST['token']);

		$login = isset($_POST['login']) && is_string($_POST['login']) ? $_POST['login'] : '';
		$item_id = intval(@$_POST['id']);
		$promo = isset($_POST['promo']) && is_string($_POST['promo']) ? $_POST['promo'] : '';
		$prefix = isset($_POST['prefix']) && is_string($_POST['prefix']) ? $_POST['prefix'] : '';

		if($item_id < 1){
			Bootstrap::response('Неверный товар', 'Ошибка!');
		}

		if(!Users::exists($login)){
			Bootstrap::response('Профиль не найден', 'Ошибка!');
		}

		$item = Items::Item()->setID($item_id);

		if(!$item->get()){
			Bootstrap::response('Товар указан неверно', 'Ошибка!');
		}

		$price = $item->getPrice();

		$data = [];

		if($item->getSurcharge() && !$item->getPrefix()){
			$listing = P::Listing()->setStatus(1)->setItemID(0)->setSort("`i`.`price` DESC")->setPlayer($login)->setLimit(1)->get();

			if($listing){
				$old = $listing[0]->getItem();

				if($price <= $old->getPrice()){
					Bootstrap::response('У игрока уже есть привилегия', 'Ошибка!');
				}

				$price = $price - $old->getPrice();
			}
		}

		if($item->getPrefix()){
			$prefix_length = mb_strlen($prefix, 'UTF-8');
			if($prefix_length < $item->getPrefixMin() || $prefix_length > $item->getPrefixMax()){
				Bootstrap::response('Неверная длина префикса', 'Ошибка!');
			}

			if(preg_match('/[\;\*]+/iu', $prefix)){
				Bootstrap::response('Неверная формат префикса', 'Ошибка!');
			}

			$data['prefix'] = $prefix;
		}

		$promo_id = 0;

		$promo = Promo::Code()->setCode($promo);

		if($promo->get()){
			if($promo->getDiscount() > 0){
				$price = $price - ($price / 100 * $promo->getDiscount());
			}

			$promo_id = $promo->getID();
		}

		$payment = P::Payment()
			->setPlayer($login)
			->setAmount(1)
			->setItemID($item->getID())
			->setPromoID($promo_id)
			->setData(json_encode($data, true))
			->setSum($price);

		if(!$payment->insert()){
			Bootstrap::response('Ошибка создания платежа', 'Ошибка!');
		}

		$config = Bootstrap::getConfig();

		$desc = "Пополнение счета игрока {$login} на сумму {$payment->getSum()} Р.";

		$sign = hash('sha256', "{$payment->getID()}{up}{$desc}{up}{$payment->getSum()}{up}{$config['unitpay']['private']}");

		$url = "https://unitpay.{$config['unitpay']['domain']}/pay/{$config['unitpay']['public']}/qiwi?sum={$payment->getSum()}&account={$payment->getID()}&desc={$desc}&signature={$sign}";

		Bootstrap::response('OK', 'OK', true, [
			'url' => $url
		]);
	}

	public function itemPrice() {
		Bootstrap::runCSRF(@$_POST['token']);

		$login = isset($_POST['login']) && is_string($_POST['login']) ? $_POST['login'] : '';
		$item_id = intval(@$_POST['id']);
		$promo = isset($_POST['promo']) && is_string($_POST['promo']) ? $_POST['promo'] : '';
		$prefix = isset($_POST['prefix']) && is_string($_POST['prefix']) ? $_POST['prefix'] : '';

		if($item_id < 1){
			Bootstrap::response('Неверный товар', 'Ошибка!');
		}

		if(!Users::exists($login)){
			Bootstrap::response('Профиль не найден', 'Ошибка!');
		}

		$item = Items::Item()->setID($item_id);

		if(!$item->get()){
			Bootstrap::response('Товар указан неверно', 'Ошибка!');
		}

		if($item->getPrefix()){
			$prefix_length = mb_strlen($prefix, 'UTF-8');
			if($prefix_length < $item->getPrefixMin() || $prefix_length > $item->getPrefixMax()){
				Bootstrap::response('Неверная длина префикса', 'Ошибка!');
			}

			if(preg_match('/[\;\*]+/iu', $prefix)){
				Bootstrap::response('Неверная формат префикса', 'Ошибка!');
			}
		}

		$price = $item->getPrice();

		$surcharge = false;

		if(!$item->getPrefix()){
			$items = Items::Listing()->setServerID($item->getServerID())->get();

			$ids = [];

			foreach($items as $item){
				$ids[] = $item->getID();
			}

			$listing = P::Listing()->setStatus(1)->setItemIDs($ids)->setSort("`i`.`price` DESC")->setPlayer($login)->setLimit(1)->get();

			if($listing){
				$old = $listing[0]->getItem();

				if($price <= $old->getPrice()){
					Bootstrap::response('У игрока уже есть привилегия', 'Ошибка!');
				}

				$price = $price - $old->getPrice();

				$surcharge = true;
			}
		}

		$promo = Promo::Code()->setCode($promo);

		if($promo->get()){
			if($promo->getDiscount() > 0){
				$price = $price - ($price / 100 * $promo->getDiscount());
			}
		}

		Bootstrap::response('OK', 'OK', true, [
			'price' => $price,
			'surcharge' => $surcharge
		]);
	}
}

?>