<?
namespace Artovenry\Wp\CustomPost;

class MethodNotFound extends \Exception{}
abstract class Base{
  use Query;
  use PostMeta;
  use Route;

  private $post;
  private $post_id;

  function __get($name){
    if($name==="post")return $this->post;
    if($name==="post_id")return $this->post_id;
    foreach(static::$meta_attrs as $attr){
      if(method_exists($this, $attr) AND $attr === $name)
        return $this->$attr();
      if($name === $attr)return $this->get_meta($attr);
    }
    return $this->post->$name;
  }

  function __call($name, $args){
    foreach(static::$meta_attrs as $attr){
      if($name === $attr)return $this->get_meta($attr);
    }
    return false;
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

  private static function register_callbacks($meta_boxes){
    add_action("save_post_" . static::$post_type, function($post_id, $post, $updated)use($meta_boxes){
      if(is_array($meta_boxes))
        foreach($meta_boxes as $item)$item->after_save($post_id, $post, $updated);
      static::after_save($post_id, $post, $updated);
    }, 10, 3);
    add_filter("wp_insert_post_data", function($data, $postarr){
      if(static::$post_type !== $data["post_type"])return $data;
      return static::before_save($data, $postarr);
    },10,2);
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

  static function after_save($post_id, $post, $updated){}//NOOP
  static function before_save($data, $postarr){return $data;}//NOOP
}