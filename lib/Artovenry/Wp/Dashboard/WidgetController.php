<?
namespace Artovenry\Wp\Dashboard;

abstract class WidgetController{
	function initialize(){
		add_action("rest_api_init", function($server){
			$namespace= "art";
			$route= "route";
			$args= [
				[
					"methods"=> \WP_REST_Server::READABLE,
					"callback"=> [$this, "cb"]
				]
			];
			$override= false;
			register_rest_route($namespace, $route, $args, $override);
		});
		add_filter("wp_rest_server_class", function(){
			return __NAMESPACE__ . "\Server";
		});
	}
	function cb(){
		exit("sghsersg");
		return new \WP_REST_Response( [], 200 );
	}
}



class Server extends \WP_REST_Server{
	function register_route($namespace, $route, $route_args, $override= false){
		parent::register_route($namespace, $route, $route_args, $override);
	}
}