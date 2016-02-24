<?
namespace Artovenry\Wp;
use \Symfony\Component\Yaml\Yaml;
require_once("CustomPost/Error.php");

class Route{
	use Route\Drawer;
	const REST_NAMESPACE= "art";
	const OPTION_NAME= "art_routes";
	private $routes;
	private $namespace;
	private static $instance;
	static function initialize(){
		self::getInstance()->register();
	}
	static function dump(){
		self::getInstance()->routes;
	}
	static function flush(){
		$routes= Yaml::parse(ART_ROUTES_YAML);
		$namespace= isset($routes["namespace"])? join("/", [self::REST_NAMESPACE, $routes["namespace"]]): self::REST_NAMESPACE;
		if(isset($routes["routes"]))
			$routes= self::draw($routes["routes"]);
		update_option(self::OPTION_NAME, compact("namespace", "routes"));
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

		private function __construct(){
			if(empty($option= get_option(self::OPTION_NAME))){
				self::flush();
				$option= get_option(self::OPTION_NAME);
			}
			$this->namespace= $option["namespace"];
			$this->routes= $option["routes"];
		}
		private function _routes(){
			foreach($this->routes as $route){
				$namespace= $this->namespace;
				$path= array_shift($route);
				$route= array_shift($route);
				$args=[
					"methods"=>$route["methods"],
					"callback"=>function($request) use($route){
						$controller= $route["controller"];
						$action= $route["action"];
						try{
							if(!isset($controller::$permitted_params)){
								$params= [];
							}else{
								$permitted_params= $controller::$permitted_params;
								$params= array_filter($request->get_params(), function($item, $key)use($permitted_params){
									//CHECK EXISTENCE STRICTLY!
									return in_array($key, $permitted_params, true);
								}, ARRAY_FILTER_USE_BOTH);
							}
							return call_user_func_array([new $controller, $action], [$params]);
						}catch(CustomPost\Error $e){
							return new \Artovenry\Wp\Response\Error($e);
						}
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