<?
namespace Artovenry\Wp;

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
	static function getInstance(){
		if(isset(self::$instance))return self::$instance;
		return new self;
	}
	function register(){
		foreach($this->routes as $route){
			$namespace= $this->namespace;
			$path= array_shift($route);
			$route= array_shift($route);
			$methods= $route["methods"];
			$callback= function($request) use($route){
				$controller= $route["controller"];
				$action= $route["action"];
				$controller::do_action($request, $action);
			};
			$permission_callback= function() use($route){
				return $this->current_user_can($route["capability"]);
			};
			register_rest_route($namespace, $path, compact(
				"methods", "callback", "permission_callback"
			));
		}
	}
	function __get($name){
		if($name ==="routes")return $this->$name;
	}

	//private
		private function __construct(){
			if(empty($option= get_option(self::OPTION_NAME))){
				self::flush();
				$option= get_option(self::OPTION_NAME);
			}
			$this->namespace= $option["namespace"];
			$this->routes= $option["routes"];
		}
		private function current_user_can($capability=null){
			if(empty($capability))return true;
			return current_user_can($capability);
		}
}