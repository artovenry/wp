<?
namespace Artovenry\Wp;
use \Symfony\Component\Yaml\Yaml;
class InvalidRoute extends \Exception{}

class Route{
	const REST_NAMESPACE= "art";
	private $routes;
	private $namespace;
	private static $instance;
	static function initialize(){
		self::getInstance()->register();
	}
	static function dump(){
		self::getInstance()->routes;
	}
	static function getInstance(){
		if(isset(self::$instance))return self::$instance;
		return new self;
	}
	function register(){
		foreach($this->_routes() as $route){
			call_user_func_array("register_rest_route", $route);
		}
	}
	function __get($name){
		if($name ==="routes")return $this->$name;
	}

	//private
		private function __construct(){
			$routes= Yaml::parse(ART_ROUTES_YAML);
			$this->namespace= isset($routes["namespace"])? join("/", [self::REST_NAMESPACE, $routes["namespace"]]): self::REST_NAMESPACE;
			if(isset($routes["routes"]))
				$this->routes= $this->draw($routes["routes"]);
		}
		private function draw($routes, $path=null, $controller=null, $capability=null){
			$rs= [];
			foreach($routes as $key=>$item){
				if(empty($item["actions"]) or !is_array($item["actions"]))continue;
				if(empty($controller) and empty($item["controller"]))continue;

				if(!empty($item["controller"]))
					$controller= $item["controller"];
				if(!empty($item["capability"]))
					$capability= $item["capability"];
				$path= empty($path)? $key: join("/", [$path, $key]);
				$actions= $item["actions"];
				$rs= array_merge($rs, $this->route_for($path, compact("controller", "capability", "actions")));
				if(!empty($item["routes"]))
					$rs= array_merge($rs,$this->draw($item["routes"], $key, $controller, $capability));
			}
			return $rs;
		}
		private function route_for($path, $hash){
			return array_map(function($item) use($path, $hash){
				extract($hash);
				$action= $item[0];
				$methods= isset($item[1])? $item[1]: ["GET"];
				if(isset($item[2]["controller"]))
					$controller= $item[2]["controller"];
				if(isset($item[2]["capability"]))
					$capability= $item[2]["capability"];
				return[$path,[
					"methods"=> $methods,
					"controller"=> $controller,		
					"action"=> $action,
					"capability"=>$capability
				]];
			}, $hash["actions"]);
		}
		private function _routes(){
			foreach($this->routes as $route){
				$namespace= $this->namespace;
				$path= array_shift($route);
				$route= array_shift($route);
				$args=[
					"methods"=>$route["methods"],
					"callback"=>function() use($route){
						$controller= $route["controller"];
						$action= $route["action"];
						call_user_func_array([new $controller, $action], func_get_args());
					},
					"permission_callback"=>function() use($route){
						if(empty($route["capability"]))return true;
						return current_user_can($route['capability']);
					},
					"args"=>[
						"id"=>[
							"sanitize_callback"=>function($param){return intval($param);}
						]
					]
				];
				yield [$namespace, $path, $args];
			}
		}
}