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
    foreach(["core", "plugins", "themes"] as $item)
      add_filter("pre_site_transient_update_{$item}", '__return_zero');
    foreach(["plugins", "themes"] as $item)
      remove_action("load-update-core.php", "wp_update_{$item}");

    add_filter("admin_footer_text", function (){return "";});
    add_filter("contextual_help",function($help, $id, $screen){
      if($id=="dashboard")$screen->remove_help_tabs();
      return $help;
    },10,3);
    add_action("screen_layout_columns",  function($columns){
      $columns['dashboard'] = $this->column_size;
      return $columns;
    },10);
    add_action("get_user_option_screen_layout_dashboard", function(){
      return $this->column_size;
    });
    add_action("wp_dashboard_setup", function(){
      global $wp_meta_boxes;
      unset($wp_meta_boxes['dashboard']);
    });
  }

}