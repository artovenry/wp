<?
namespace Artovenry\Wp\CustomPost;

trait Extractor{
  static function options_for($name){
      if(isset(static::$$name))
        return (array) static::$$name;
      $method= join("::", [get_called_class(), $name]);
      if(is_callable($method))
        return (array) $method();
      return [];
  }
}

abstract class Base{
  use Query;
  use Extractor;
  const DEFAULT_POST_TYPE_OPTIONS=[
    "public"=>true,
    "supports"=>["title", "editor", "author", "thumbnail",  "excerpt",  "revisions"],
    "hierarchical"=>false,
    "rewrite"=>false
  ];
  static function initialize(){
    self::register_post_type();
  }
  static function post_type(){
    return static::$post_type["name"];
  }
  static function build($post_or_post_id){
    return new static($post_or_post_id);
  }
  static function is_attr_defined($attr, $raise= false){
    $meta_attrs= isset(static::$meta_attrs)? static::$meta_attrs: [];
    foreach($meta_attrs as $item)
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
    private static function register_post_type(){
      if(!empty($options= self::options_for("post_type")))
        return false;
      if(is_string(static::$post_type))$options= ["name"=> static::$post_type];
      if(empty($options["label"]))$options["label"]= $options["name"];
      $options= array_merge(self::DEFAULT_POST_TYPE_OPTIONS, $options);
      $meta_boxes= array_map(function($options){
        return new MetaBox($options);
      }, self::options_for("meta_boxes"));
      $options["register_meta_box_cb"]= function()use($meta_boxes){
        Haml::initialize(MetaBox::VIEWPATH);
        foreach($meta_boxes as $item)$item->register();
      };
      add_action("init", function()use($options){
        register_post_type($options["name"], $options);
      });
    }
    private function __construct($post_or_post_id){
      $p= $post_or_post_id;
      $this->post= is_int($p)? get_post($p): $p;
      $this->post_id= is_int($p)? $p: $p->ID;
    }
}
