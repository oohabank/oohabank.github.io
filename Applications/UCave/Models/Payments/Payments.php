<?php

namespace App\Models\Payments;

use App\Bootstrap;

class Payments {

	const PAGINATION_LIMIT = 20;

	public static function Payment(?array $import = null) : Payment {
		return new Payment($import);
	}

	public static function Listing() : Listing {
		return new Listing();
	}

	public static function UnitpayResponse($message='', $type=0){
		if($type===1){
			$response['result']['message'] = $message;
		}else{
			$response['error']['message'] = $message;
		}

		echo json_encode($response); exit;
	}

	public static function UnitpaySign($method='check', $params=[]) {
		if($method != 'check' && $method != 'pay'){
			$method = 'wrong';
		}

		ksort($params);

		unset($params['sign']);

		unset($params['signature']);

		$config = Bootstrap::getConfig();

		array_push($params, $config['unitpay']['private']);

		array_unshift($params, $method);

		return hash('sha256', join('{up}', $params));
	}
}

?>