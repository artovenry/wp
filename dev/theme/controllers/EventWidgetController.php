<?
class EventWidgetController extends Artovenry\Wp\AjaxController{
	function index(){
		return $this->render(["events"=>Event::take(1)]);
	}
	function read(){
		return $this->render(["event"=>Event::find($this->params["id"])]);
	}
	function create(){
	}
	function update(){
	}
	function delete(){}

	//private
	//private function event_params(){
	//	return $this->params->required("event")->permit(["scheduled_on", "hoge"]);
}
