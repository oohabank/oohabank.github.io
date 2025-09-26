<?php

ini_set('mysql.connect_timeout', 3);
ini_set('display_errors', 1);
error_reporting(E_ALL);

define('__ROOT__', __DIR__);

$app = (require(__ROOT__.'/Application.php'));

$config = (require(__ROOT__.$app['app_config_filename']));

$connection =  new mysqli($config['database']['host'], $config['database']['user'], $config['database']['password'], $config['database']['database'], $config['database']['port']);

if($connection->connect_errno){
	exit("MySQLi connection error: {$connection->connect_error}");
}

if(!@$connection->options(MYSQLI_OPT_CONNECT_TIMEOUT, 3)){
	exit("MySQLi error set options: {$connection->connect_error}");
}

if(!@$connection->set_charset($config['database']['charset'])){
	exit("MySQLi change database charset error: {$connection->error}");
}

$select = $connection->query("SELECT `p`.*,
									`i`.`command`,
									`s`.`rcon_host`, `s`.`rcon_password`, `s`.`rcon_port`
							FROM `payments` AS `p`
							INNER JOIN `items` AS `i`
								ON `i`.`id` = `p`.`item_id`
							INNER JOIN `servers` AS `s`
								ON `s`.`id` = `i`.`server_id`
							WHERE `p`.`done` != 1 AND `p`.`status` = 1
							ORDER BY `p`.`id` ASC
							LIMIT 50");

if(!$select || !$select->num_rows){
	exit('EMPTY');
}

require(__ROOT__.'/Applications/'.$app['app_name'].'/Models/Rcon.php');

$classname = "{$app['app_namespace']}\\Models\\Rcon";

$rcon = new $classname();

$toupdate = [];

while($payment = $select->fetch_assoc()){
	$payment_id = intval($payment['id']);

	$item = str_replace('; ', ';', $payment['command']);

	$item = str_replace(['{PLAYER}', '{AMOUNT}'], [$payment['player'], floatval($payment['amount'])], $item);

	$items = explode(';', $item);

	$rcon->setOptions($payment['rcon_host'], $payment['rcon_port'], $payment['rcon_password'], 5);

	if(!$rcon->connect()){ continue; }

	$send = false;

	foreach($items as $command){
		$send = $rcon->send_command($command);
	}

	if($send){
		$toupdate[] = $payment_id;
	}
}

if(empty($toupdate)){
	exit('Nothing to update');
}

$toupdate = implode(',', $toupdate);

$update = $connection->query("UPDATE `payments` SET `done` = 1 WHERE `id` IN ({$toupdate})");

if(!$update){
	exit('Error SQL update');
}

exit('SUCCESS');

?>