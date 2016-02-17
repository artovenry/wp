<?
namespace Artovenry\Wp;
class Dashboard{
	const DEFAULT_COLUMN_SIZE= 1;

  protected function __construct(){
    $this->setup();
    $this->render();
  }

  static function init($options=[]){
    new static;
  }

  function render(){
    add_action("wp_dashboard_setup", function(){
      //add_meta_box($name, $label, function(){}, get_current_screen(), "normal", "core");
    });
  }

  //[FIXME]
  function setup(){
    //[FIXME]

  }

}