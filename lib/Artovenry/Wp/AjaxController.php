<?
namespace Artovenry\Wp;
require_once "errors.php";

abstract class AjaxController{
	protected $params;
	protected $action;
	protected $request;

	function render($arg=[]){
		return new \WP_REST_Response((new Support\Conversion($arg))->to_a());
	}
	static function do_action($request, $action){
		$instance= new static($request, $action);
		$instance->$action();
	}
	//private
		private function __construct($request, $action){
			//TODO: implement strong parameter
			//$this->params= new Parameter($request->get_params());
			$this->params= $request->get_params();
			$this->action= $action;
			$this->request= $request;
		}		
}


//TODO: implement strong parameter
class Parameter extends Support\ArrayObject{
	private $permitted= false;
	function __construct($params){
		parent::__consruct((array)$params);
	}
/*	function required($key){
		$value= $this[$key];
		if(!empty($value) or $value === false)
			return is_array($value)? new self($value): new self([]);
		throw new ParameterMissing($key);
	}

	function permit($filters=null){

	}
	function permit_all(){


	}
	function permitted(){
		return $this->$permitted;
	}
*/
}

__halt_compiler();
----------------------------------------------------------
params=[event: [schedule_on: 20], hoge: 3, post_date: yyyymmdd, ]

params->required("hoge") # params= [3]



$callback= function($request) use($route){
	$controller= $route["controller"];
	$action= $route["action"];
	try{
\		return call_user_func_array([new $controller, $action], [$params]);
	}catch(CustomPost\Error $e){
		return new \Artovenry\Wp\Response\Error($e);
	}
};
