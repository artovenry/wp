<?
class EventWidgetController extends Artovenry\Wp\Dashboard\WidgetController{
	static $permitted_params=[
		"id", "hello"
	];

	function index(){
		return $this->render(["events"=>Event::take(1)]);
	}
	function read($params){
		return $this->render(["event"=>Event::find($params["id"])]);
	}
	function create($params){
		return $this->render($params["hello"]);
	}
	function update($params){
		return $this->render($params["hello"]);
	}
	function delete(){}
}
