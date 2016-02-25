<?
namespace Artovenry\Wp\Initializer;

trait WpInitializer{
  function initialize_wp(){
    extract($this->options);
    if($disable_update_notification)
      $this->disable_update_notification();
    if($check_version)
      \Artovenry\Wp\Version::run();
    if($sweep_wp_header)
      $this->sweep_wp_header();
    if($disable_update_notification)
      $this->disable_auto_emoji_translation();
    if($sweep_dashboard)
      $this->sweep_dashboard($dashboard_column_size);
  }
	private function disable_update_notification(){
    foreach(["core", "plugins", "themes"] as $item)
      add_filter("pre_site_transient_update_{$item}", '__return_zero');
    foreach(["plugins", "themes"] as $item)
      remove_action("load-update-core.php", "wp_update_{$item}");
	}
	private function sweep_wp_header(){
	  remove_action('wp_head', 'wp_generator');
	  remove_action('wp_head', 'wlwmanifest_link');
	  remove_action('wp_head', 'rsd_link');
	}
	private function disable_auto_emoji_translation(){
	  remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	  remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	  remove_action( 'wp_print_styles', 'print_emoji_styles' );
	  remove_action( 'admin_print_styles', 'print_emoji_styles' );    
	  remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	  remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );    
	  remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );			
	}
	private function sweep_dashboard($column_size){
    add_action("wp_dashboard_setup", function(){
      global $wp_meta_boxes;
      unset($wp_meta_boxes['dashboard']);
    });
    add_filter("admin_footer_text", function (){return "";});
    add_filter("contextual_help",function($help, $id, $screen){
      if($id=="dashboard")$screen->remove_help_tabs();
      return $help;
    },10,3);
    add_action("screen_layout_columns",  function($columns) use($column_size){
      $columns['dashboard'] = $column_size;
      return $columns;
    },10);
    add_action("get_user_option_screen_layout_dashboard", function() use($column_size){
      return $column_size;
    });
	}
}