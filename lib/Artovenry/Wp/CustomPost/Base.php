<?
namespace Artovenry\Wp\CustomPost;

trait Extractor{
  static function options_for($name){
      if(isset(static::$$name))
        return (array) static::$$name;
      $method= join("::", [get_called_class(), $name]);
      if(is_callable($method))
        return (array)call_user_func($method);
      return [];
  }
}

abstract class Base{
  use Query;
  use PostMeta;
  use Extractor;
  const DEFAULT_META_PREFIX= "art";
  const DEFAULT_POST_TYPE_OPTIONS=[
    "public"=>true,
    "supports"=>["title", "editor", "author", "thumbnail",  "excerpt",  "revisions"],
    "hierarchical"=>false,
    "rewrite"=>false
  ];
  static function initialize(){
    $meta_boxes= array_map(function($item){
      return new MetaBox(get_called_class(), $item);
    }, self::options_for("meta_boxes"));
    self::register_post_type($meta_boxes);
    self::register_callbacks($meta_boxes);
    self::register_posts_list_table();
    self::register_routes();
  }
  static function post_type(){
    return static::$post_type["name"];
  }
  static function build($post_or_post_id){
    return new static($post_or_post_id);
  }
  static function meta_prefix(){
    if(empty($opt= static::options_for("post_type")))
      return self::DEFAULT_META_PREFIX;
    if(empty($opt["meta_prefix"]))
      return self::DEFAULT_META_PREFIX;
    return $opt["meta_prefix"];
  }
  static function is_attr_defined($attr, $raise= false){
    $attributes= static::options_for("meta_attributes");
    foreach($attributes as $item)
      if($item === $attr)return true;
    if($raise)throw new AttributeNotDefined($attr);
    return false;
  }
  function __get($name){
    if($name==="post")return $this->post;
    if($name==="post_id")return $this->post_id;
    foreach(self::options_for("meta_attributes") as $attr)
      if($name === $attr)return $this->get_meta($attr);
    return $this->post->$name;
  }
  function is_auto_draft(){
    return $this->post->post_status === "auto-draft";
  }

  //private
    private static function register_post_type($meta_boxes){
      if(empty($options= self::options_for("post_type")))
        return false;
      if(is_string(static::$post_type))$options= ["name"=> static::$post_type];
      if(empty($options["label"]))$options["label"]= $options["name"];
      $options= array_merge(self::DEFAULT_POST_TYPE_OPTIONS, $options);
      $options["register_meta_box_cb"]= function()use($meta_boxes){
        Haml::initialize(MetaBox::VIEWPATH);
        foreach($meta_boxes as $item)$item->register();
      };
      add_action("init", function()use($options){
        register_post_type($options["name"], $options);
      });
    }
    private static function register_callbacks($meta_boxes){
      $after_save= function() use($meta_boxes){
        call_user_func_array([new Callback(get_called_class(), $meta_boxes), "after_save"], func_get_args());
        if(is_callable($cb= get_called_class() . "::after_save"))
          call_user_func_array($cb, func_get_args());
      };
      $before_save= function($data, $postarr){
        if(static::post_type() !== $data["post_type"])return $data;
        if(is_callable($cb= get_called_class() . "::before_save"))
          return call_user_func_array($cb, func_get_args());
        return $data;
      };
      add_action("save_post_" . static::post_type(),$after_save ,10, 3);
      add_filter("wp_insert_post_data",$before_save ,10, 2);
    }
    private static function register_posts_list_table(){
      add_action("load-edit.php", function(){
        $inistance= new PostsListTable(get_called_class());
        $post_type= static::post_type();
        add_filter("manage_edit-{$post_type}_columns",[$inistance, "register_columns"]);
        add_action("manage_{$post_type}_posts_custom_column", [$inistance, "render"], 10, 2);
      });
    }
    private static function register_routes(){
      $route= new Route(static::post_type(), static::options_for("routes"));
      add_action("init", [$route, "draw"]);
      add_action("after_switch_theme", "flush_rewrite_rules");
    }
    private function __construct($post_or_post_id){
      $p= $post_or_post_id;
      $this->post= is_int($p)? get_post($p): $p;
      $this->post_id= is_int($p)? $p: $p->ID;
    }
}
