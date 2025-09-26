<?php

namespace Component\Router;

use Component\Path\Path;

class Router {
	private $uri, $current, $routes;

	private $default = 'notfound';

	public function __construct(?string $uri = null, ?array $routes = null) {
		if(!is_null($uri)){
			$this->setURI($uri);
		}

		if(!is_null($routes)){
			$this->setRoutes($routes);
		}
	}

	public function setRoutes(array $routes) : self {
		$this->routes = $routes;

		return $this;
	}

	public function getRoutes() : array {
		if(!is_null($this->routes)){
			return $this->routes;
		}

		$path = Path::to('/Routes');

		$this->routes = [];

		foreach(scandir($path) as $name){
			if($name == '..' || $name == '.'){ continue; }

			$file = (include("{$path}/{$name}"));

			$this->routes = array_merge($this->routes, $file);
		}

		return $this->routes;
	}

	public function setDefault(string $page) : self {
		$this->default = $page;

		return $this;
	}

	public function getDefault() : string {
		return $this->default;
	}

	public function setURI(string $uri) : self {
		$this->uri = $uri;

		return $this;
	}

	public function getURI() : string {
		if(is_null($this->uri)){
			if(isset($_SERVER['REQUEST_URI'])){
				$this->uri = $_SERVER['REQUEST_URI'];
			}else{
				$this->uri = "/?";
			}

			$s1 = mb_substr($this->uri, 0, 1, 'UTF-8');
			$s2 = mb_substr($this->uri, 0, 2, 'UTF-8');

			if($s2 == '/?'){
				$this->uri = mb_substr($this->uri, 2, null, 'UTF-8');
			}elseif($s1 == '/'){
				$this->uri = mb_substr($this->uri, 1, null, 'UTF-8');
			}

			if(preg_match('/^balance/iu', $this->uri)){
				$this->uri = urldecode($this->uri);
			}
		}

		return $this->uri;
	}

	public function setCurrent(array $route) : self {
		if(!isset($route['action'])){
			$route['action'] = 'index';
		}

		$this->current = $route;

		return $this;
	}

	public function getCurrent() : array {
		if(!is_null($this->current)){ return $this->current; }

		$this->execute();

		return $this->current;
	}

	public function execute() {
		$routes = $this->getRoutes();

		if(empty($routes)){ throw new RouterException('Routes not found'); }

		$uri = $this->getURI();

		$current = null;

		$method = @$_SERVER['REQUEST_METHOD'];

		foreach($routes as $k => $v){
			if(!isset($v['pattern']) || empty($v['pattern'])){ continue; }

			if(!preg_match($v['pattern'], $uri)){ continue; }

			if(!isset($v['method'])){
				$v['method'] = ['GET', 'POST'];
			}

			if(is_array($v['method'])){
				if(!in_array($method, $v['method'])){
					continue;
				}
			}else{
				if($v['method'] != $method){
					continue;
				}
			}

			if(!isset($v['action'])){
				$v['action'] = 'index';
			}

			if(!isset($v['params'])){
				$v['params'] = [];
			}

			$this->setCurrent($v);

			break;
		}

		if(is_null($this->current)){
			if(!isset($routes[$this->getDefault()])){
				throw new RouterException('Default router not found');
			}else{
				$this->setCurrent($routes[$this->getDefault()]);
			}
		}
	}
}

?>