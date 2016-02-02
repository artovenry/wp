<?
namespace Artovenry\Wp\CustomPost;
require_once __DIR__ . "/Error.php";

abstract class Base{
  use Query;
  use PostMeta;
  use Route;
  use Callback;
  private $post;
  private $post_id;

  function __get($name){
    if($name==="post")return $this->post;
    if($name==="post_id")return $this->post_id;
    if(is_array(static::$meta_attrs))
      foreach(static::$meta_attrs as $attr)
        if($name === $attr)return $this->get_meta($attr);
    return $this->post->$name;
  }

  function is_auto_draft(){
    return $this->post->post_status === "auto-draft";
  }

  static function build($post_or_post_id){
    return new static($post_or_post_id);
  }

  static function register(){
    $meta_boxes= MetaBox::init(get_called_class());
    static::register_post_type($meta_boxes);
    static::register_callbacks($meta_boxes);
    static::register_routes();
  }

  private static function register_post_type($meta_boxes){
    $options= static::$post_type_options;
    if(empty($options))$options= [];
    if(empty($options["label"])) $options["label"]= static::$post_type;
    $options= array_merge(Constants::DEFAULT_POST_TYPE_OPTIONS, $options);
    $options["register_meta_box_cb"]= function()use($meta_boxes){
      if(is_array($meta_boxes))
        foreach($meta_boxes as $meta_box)
          $meta_box->register();
    };
    add_action("init", function()use($options){
      register_post_type(static::$post_type, $options);
    });
  }
  private function __construct($post_or_post_id){
    $p= $post_or_post_id;
    $this->post= is_int($p)? get_post($p): $p;
    $this->post_id= is_int($p)? $p: $p->ID;
  }


  static function is_attr_defined($attr, $raise= false){
    $meta_attrs= isset(static::$meta_attrs)? static::$meta_attrs: [];
    foreach($meta_attrs as $item)
      if($item === $attr)return true;
    if($raise)throw new AttributeNotDefined($attr);
    return false;
  }

  static function meta_key_for($attr){
    return Constants::PREFIX . "_" . static::$post_type . "_" . $attr;
  }

}