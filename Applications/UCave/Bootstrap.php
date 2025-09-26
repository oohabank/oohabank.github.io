<?php

namespace App;

use App\Models\Users\Users;
use Component\Cache\Cache;
use Component\Path\Path;
use Component\Router\Router;
use Component\Router\RouterException;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;
use Twig\TwigFunction;

class Bootstrap {

	private static $paginations = [];

	/** @var $router Router */
	public static $router;

	public static function redirect(string $url = '/'){
		header("Location: {$url}"); exit;
	}

	public static function response(string $text = '', string $title = '', bool $type = false, array $data = []) : string {
		echo json_encode([
			'text' => $text,
			'title' => $title,
			'type' => $type,
			'data' => $data
		], JSON_PRETTY_PRINT);

		exit;
	}

	public static function templaterLoader() : FilesystemLoader {

		return Cache::has('TEMPLATER_LOADER') ? Cache::get('TEMPLATER_LOADER') : Cache::set('TEMPLATER_LOADER', new FilesystemLoader(Path::getRoot()."/Public/Themes/Default/"));
	}

	public static function templaterEnv() : Environment {
		$cache = Cache::get('TEMPLATER_ENVIROMENT');
		if(!is_null($cache)){ return $cache; }

		return Cache::set('TEMPLATER_ENVIROMENT', new Environment(self::templaterLoader()));
	}

	public static function getConfig() : array {
		$cache = Cache::get(__METHOD__);
		if(!is_null($cache)){ return $cache; }

		return Cache::set(__METHOD__, (include(Path::to('/Config.php'))));
	}

	private static function filters() {
		$env = self::templaterEnv();

		$config = self::getConfig();

		$env->addFunction(new TwigFunction('dump', function($value){
			ob_start();
			var_dump($value);
			return ob_get_clean();
		}));

		$filter_image = new TwigFilter('image', function($e){
			$config = self::getConfig();

			return empty($e) ? "{$config['meta']['site_url']}Themes/Default/img/noimg.png" : $e;
		});

		$function_inArray = new TwigFunction('inArray', function($value, array $list){
			return in_array($value, $list);
		});

		$env->addGlobal('__CONFIG__', $config);
		$env->addGlobal('__TOKEN__', md5(Users::IP().$config['csrfString']));
		$env->addGlobal('__SERVER__', $_SERVER);
		$env->addFunction($function_inArray);
		$env->addFilter($filter_image);
	}

	public static function run(Router $router) {

		self::$router = $router;

		self::filters();

		try {
			$router->execute();
		}catch(RouterException $e){
			echo $e->getMessage(); exit;
		}

		$current = $router->getCurrent();

		if(isset($current['page']) && !empty($current['page'])){
			echo self::templaterEnv()->render($current['page']);
		}elseif(!isset($current['classname']) || empty($current['classname'])){
			exit('Classname is not set');
		}else{
			$classname = "App\\{$current['classname']}";

			if(!class_exists($classname)){
				exit("Class {$classname} not found");
			}

			$class = new $classname();

			if(!method_exists($class, $current['action'])){
				exit("Action {$current['action']} not found");
			}
			$class->{$current['action']}();
		}
	}

	public static function pagination(int $page = 1, string $url = '/?page={NUM}', int $count = 10, int $limit = 10) : array {
		$token = md5("{$page}:{$url}:{$count}:{$limit}");
		if(isset(self::$paginations[$token])){
			return self::$paginations[$token];
		}

		if(!$page){ $page = 1; }

		$offset = ($page - 1) * $limit;

		$pages = ceil($count / $limit);

		if(!$pages){
			$pages = 1;
		}

		$list = [];

		for($i = 1; $i <= $pages; $i++){
			$list[] = [
				'page' => $i,
				'url' => str_replace('{NUM}', $i, $url),
				'active' => $i == $page
			];
		}

		self::$paginations[$token] = [
			'offset' => $offset,
			'limit' => $limit,
			'pages' => $pages,
			'page' => $page,
			'url' => $url,
			'list' => $list
		];

		return self::$paginations[$token];
	}

	public static function runCSRF(?string $token){
		if(!is_string($token)){
			self::response('Проверка не пройдена');
		}

		$config = self::getConfig();

		$hash = md5(Users::IP().$config['csrfString']);

		if($hash !== $token){
			self::response('Проверка не пройдена');
		}
	}

	public static function getRequest(string $url) : ?array {
		$c = curl_init($url);

		curl_setopt_array($c, [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_BINARYTRANSFER => true,
			CURLOPT_AUTOREFERER => true,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_CONNECTTIMEOUT => 3,
			CURLOPT_TIMEOUT => 3,
			CURLOPT_MAXREDIRS => 3,
			CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0'
		]);

		$result = curl_exec($c);

		curl_close($c);

		$result = @json_decode($result, true);

		if(!$result){ return null; }

		return $result;
	}

	private static $symbols = [
		'russian' => ['а'=>'a','А'=>'A',
			'б'=>'b','Б'=>'B',
			'в'=>'v','В'=>'V',
			'г'=>'g','Г'=>'G',
			'д'=>'d','Д'=>'D',
			'е'=>'e','Е'=>'E',
			'ж'=>'zh','Ж'=>'ZH',
			'з'=>'z','З'=>'Z',
			'и'=>'i','И'=>'I',
			'й'=>'y','Й'=>'Y',
			'к'=>'k','К'=>'K',
			'л'=>'l','Л'=>'L',
			'м'=>'m','М'=>'M',
			'н'=>'n','Н'=>'N',
			'о'=>'o','О'=>'O',
			'п'=>'p','П'=>'P',
			'р'=>'r','Р'=>'R',
			'с'=>'s','С'=>'S',
			'т'=>'t','Т'=>'T',
			'у'=>'u','У'=>'U',
			'ф'=>'f','Ф'=>'F',
			'х'=>'h','Х'=>'H',
			'ц'=>'c','Ц'=>'TS',
			'ч'=>'ch','Ч'=>'CH',
			'ш'=>'sh','Ш'=>'SH',
			'щ'=>'sch','Щ'=>'SHC',
			'ъ'=>'','Ъ'=>'',
			'ы'=>'i','Ы'=>'I',
			'ь'=>'','Ь'=>'',
			'э'=>'e','Э'=>'E',
			'ю'=>'yu','Ю'=>'YU',
			'я'=>'ya','Я'=>'YA'],

		'ukrainian' => [
			'і'=>'i','І'=>'I',
			'ї'=>'yi','Ї'=>'YI',
			'є'=>'e','Є'=>'E'

		]
	];

	public static function toLatin($string, $except_to='-'){

		foreach(self::$symbols as $lang => $symbol){
			$string = strtr($string, $symbol);
		}

		return preg_replace('/[^a-zA-Z0-9-]/iu', $except_to, $string);
	}

	public static function array_group(array $array, string $column, string $type = 'array') : array {
		$list = [];

		foreach($array as $assoc){

			if($type == 'object'){
				if(method_exists($assoc, $column)){
					$columnval = $assoc->$column();
				}else{
					continue;
				}
			}else{
				if(!isset($assoc[$column])){ continue; }

				$columnval = $assoc[$column];
			}


			if(!isset($list[$columnval])){
				$list[$columnval] = [];
			}

			$list[$columnval][] = $assoc;
		}

		return $list;
	}
}

?>