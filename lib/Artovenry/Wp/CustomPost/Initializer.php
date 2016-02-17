<?
namespace Artovenry\Wp\CustomPost;

class Initializer{
	function __construct($class){
    $meta_boxes= array_map(function($item) use($class){
      return new MetaBox($class, $item);
    }, $class::options_for("meta_boxes"));
    $this->register_post_type($class, $meta_boxes);
    $this->register_callbacks($class, $meta_boxes);
    $this->register_posts_list_table($class);
    $this->register_routes($class);
	}

	//private
	  private function register_post_type($class, $meta_boxes){
	    if(empty($options= $class::options_for("post_type_options")))
	      return false;
	    if(isset($class::$post_type_options) and is_string($class::$post_type_options))
	      $options= ["name"=> static::$post_type_options];
	    if(empty($options["label"]))$options["label"]= $options["name"];
	    $options= array_merge(Base::DEFAULT_POST_TYPE_OPTIONS, $options);
	    $options["register_meta_box_cb"]= function()use($meta_boxes){
	      Haml::initialize(MetaBox::VIEWPATH);
	      foreach($meta_boxes as $item)$item->register();
	    };
	    add_action("init", function()use($options){
	      register_post_type($options["name"], $options);
	    });
	  }
    private static function register_callbacks($class, $meta_boxes){
      $after_save= function() use($class, $meta_boxes){
        call_user_func_array([new Callback($class, $meta_boxes), "after_save"], func_get_args());
        if(is_callable($cb= $class . "::after_save"))
          call_user_func_array($cb, func_get_args());
      };
      $before_save= function($data, $postarr) use($class){
        if($class::post_type() !== $data["post_type"])return $data;
        if(is_callable($cb= $class . "::before_save"))
          return call_user_func_array($cb, func_get_args());
        return $data;
      };
      add_action("save_post_" . $class::post_type(),$after_save ,10, 3);
      add_filter("wp_insert_post_data",$before_save ,10, 2);
    }
    private static function register_posts_list_table($class){
      add_action("load-edit.php", function() use($class){
        $inistance= new PostsListTable($class);
        $post_type= $class::post_type();
        add_filter("manage_edit-{$post_type}_columns",[$inistance, "register_columns"]);
        add_action("manage_{$post_type}_posts_custom_column", [$inistance, "render"], 10, 2);
      });
    }
    private static function register_routes($class){
      $route= new Route($class::post_type(), $class::options_for("routes"));
      add_action("init", [$route, "draw"]);
      add_action("after_switch_theme", "flush_rewrite_rules");
    }

}
