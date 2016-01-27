<?
namespace Artovenry\Wp\CustomPost;

trait Route{
  static function register_routes(){
    add_action("after_switch_theme", "flush_rewrite_rules");
    add_action("init", function(){
    	foreach(static::default_routes() as $route)
    		add_rewrite_rule($route[0], $route[1]);
    	if(isset(static::$routes) AND is_array(static::$routes))
	    	foreach(static::$routes as $route)
  	  		add_rewrite_rule($route[0], $route[1]);
    });
    add_filter("post_type_link", function($url, $post){
    	return static::permalink($url, $post);
    },10, 2);
  }

  static function default_routes(){
    $post_type= static::$post_type;
    return [
      ["{$post_type}/?$", 'index.php?post_type=' . $post_type],
      ["{$post_type}/(\d+)/?$", 'index.php?p=$matches[1]&post_type=' . $post_type],
      ["{$post_type}/archive/(\d{4})/(\d{1,2})/?$", 'index.php?year=$matches[1]&monthnum=$matches[2]&post_type=' . $post_type],
      ["{$post_type}/archive/(\d{4})/?$", 'index.php?year=$matches[1]&post_type=' . $post_type],
    ];
  }

  static function permalink($url, $post){
    $post_type= static::$post_type;
    if($post->post_type !== $post_type)return $url;
    $status= get_post_status($post->ID);
    if(in_array($status, ['draft', 'pending', 'auto-draft', 'future']))
      return home_url(add_query_arg([
        "post_type"=>$post_type,
        "p"=>$post->ID
      ], ""));
    return home_url("{$post_type}/{$post->ID}");
  }


}