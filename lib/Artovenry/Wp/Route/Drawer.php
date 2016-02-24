<?
namespace Artovenry\Wp\Route;
trait Drawer{
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
			return[$path,[
				"methods"=> $methods,
				"controller"=> $controller,		
				"action"=> $action,
				"capability"=>$capability
			]];
		}, $hash["actions"]);
	}
}