<?
function stylesheet_link_tag($name="", $media="screen"){
  $filename= empty($name)? "site" : $name;
  $dev= ART_ENV == "development" ? "-dev" : "";
  $filename= $filename . $dev . ".css";
  $url= get_stylesheet_directory_uri() . "/css/$filename";
  $format= '<link rel="stylesheet" media="%s" href="%s">';
  return sprintf($format, $media, $url);
}

function javascript_include_tag($name=""){
  $filename= empty($name)? "site" : $name;
  $dev= ART_ENV == "development" ? "-dev" : "";
  $filename= $filename . $dev . ".js";
  $url= get_stylesheet_directory_uri() . "/js/$filename";
  $format= '<script type="text/javascript" src="%s"></script>';
  return sprintf($format, $url);
}


function remove_buildin_scripts(){
  remove_action('wp_head', 'wp_generator');
  remove_action('wp_head', 'wlwmanifest_link');
  remove_action('wp_head', 'rsd_link');
  remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
  remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
  remove_action( 'wp_print_styles', 'print_emoji_styles' );
  remove_action( 'admin_print_styles', 'print_emoji_styles' );    
  remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
  remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );    
  remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
}

function remove_update_notifications(){
  foreach(["core", "plugins", "themes"] as $item){
    add_filter("pre_site_transient_update_{$item}"," __return_zero");
    if($item !== "core")
      remove_action("load-update-core.php", "wp_update_{$item}");
  }
}