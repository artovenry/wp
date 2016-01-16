<?
namespace Artovenry\Wp;

//TODO メニュー位置は一番上！
//TODO REQUIRE Artovenry\Haml

abstract class PostType{
  use PostType\Rewrite;

  const SUPPORTS= ["title", "editor", "author", "thumbnail",  "excerpt",  "revisions"];
  const HIERARCHICAL= false; 
  const REWRITE= false;

  static $meta_boxes= [];
  protected static $public= true;
  protected static $supports= self::SUPPORTS;

  static function run($classes){
    add_action("after_switch_theme", "flush_rewrite_rules");
    foreach($classes as $class){
      foreach($class::$meta_boxes as $attr=>&$value)
        //$value= new PostType\MetaBox($class::$post_type, $attr, $value["label"], $value["priority"], $value["context"]);
        $value= new PostType\MetaBox($class::$post_type, $attr, $value);
      add_action("init", "{$class}::add_post_type");
      add_filter("post_type_link", "{$class}::permalink", 10, 2);
      add_action("save_post_{$class::$post_type}",  "{$class}::_after_save", 10, 2);
      add_filter("wp_insert_post_data", "{$class}::_before_save");
    }
  }
  static function _before_save($data){
    if(static::$post_type !== $data["post_type"])return $data;
    foreach(static::$meta_boxes as $attr=>$meta_box)
      $data= static::before_save($data, $attr, $_POST[$meta_box->meta_key()]);
    return $data;
  }
  static function before_save($data, $attr){return $data;}

  static function _after_save($post_id, $post){
    if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return false;
    foreach(static::$meta_boxes as $attr=>$meta_box){
      if(!$meta_box->is_authorized($post_id))return false;
      $meta_box->after_save($post);
      static::after_save($post, $attr, $_POST[$meta_box->meta_key()]);
    }
  }
  //NOOP
  static function after_save($post, $attr){}

  static function add_post_type(){
    register_post_type(static::$post_type, static::post_type_options());
    static::add_rewrite_rules();
  }

  static function register_meta_boxes(){
    foreach(array_values(static::$meta_boxes) as $meta_box)
      $meta_box->register();
  }

  private static function post_type_options(){
    return [
      "label"=>static::$label,
      "public"=> static::$public,
      "supports"=>static::$supports,
      "rewrite"=>self::REWRITE,
      "hierarchical"=>self::HIERARCHICAL,
      "register_meta_box_cb"=>  get_called_class(). "::register_meta_boxes"
    ];
  }


}
