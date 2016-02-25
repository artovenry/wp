<?
namespace Artovenry\Wp\Route;
use \Symfony\Component\Yaml\Yaml;
use \Artovenry\Wp\Error;

require_once dirname(__DIR__) . "/errors.php";
class ControllerNotDefined extends Error{}
class ActionNotDefined extends Error{}

trait Drawer{
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
	private static function draw($routes, $path=null, $controller=null, $capability=null){
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
			$rs= array_merge($rs, self::route_for($path, compact("controller", "capability", "actions")));
			if(!empty($item["routes"]))
				$rs= array_merge($rs,self::draw($item["routes"], $key, $controller, $capability));
		}
		return $rs;
	}

	private static function route_for($path, $hash){
		return array_map(function($item) use($path, $hash){
			extract($hash);
			$action= $item[0];
			$methods= isset($item[1])? $item[1]: ["GET"];
			if(isset($item[2]["controller"]))
				$controller= $item[2]["controller"];
			if(isset($item[2]["capability"]))
				$capability= $item[2]["capability"];
			if(!class_exists($controller))
				throw new ControllerNotDefined($controller);
			if(!method_exists($controller, $action))
				throw new ActionNotDefined($controller, $action);
			return[$path,[
				"methods"=> $methods,
				"controller"=> $controller,		
				"action"=> $action,
				"capability"=>$capability
			]];
		}, $hash["actions"]);
	}
}