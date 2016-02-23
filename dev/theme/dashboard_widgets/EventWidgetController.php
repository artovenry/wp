<?
class EventWidgetController extends Artovenry\Wp\Dashboard\WidgetController{
	function index(){
		$events= Event::take(10);
		return $this->render(["events"=>$events]);
	}
	function read($id){
		$event= Event::find($id);
		return $this->render(["event"=>$event]);
	}
	function create(){
		
	}
	function show(){}
	function update(){}
	function delete(){}
}
