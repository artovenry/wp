<?
namespace Artovenry\Wp;

//TODO メニュー位置は一番上！
//TODO REQUIRE Artovenry\Haml


abstract class PostType{
  use PostType\Rewrite;

  const SUPPORTS= ["title", "editor", "author", "thumbnail",  "excerpt",  "revisions"];
  const HIERARCHICAL= false; 
  const REWRITE= false;

  static $meta_box_classes= [];
  protected static $public= true;
  protected static $supports= self::SUPPORTS;

  static function run($classes){
    add_action("after_switch_theme", "flush_rewrite_rules");
    foreach($classes as $class){
      foreach($class::$meta_boxes as $attr=>$value)
        $class::add_meta_box_classes($attr, new PostType\MetaBox($class, $attr, $value));
      add_action("init", "{$class}::add_post_type");
      add_filter("post_type_link", "{$class}::permalink", 10, 2);
      add_action("save_post_{$class::$post_type}",  "{$class}::_after_save", 10, 2);
      add_filter("wp_insert_post_data", "{$class}::_before_save");
    }
  }

  static add_meta_box_classes($attr, $object){
    static::$meta_box_classes[$attr]= $object;
  }

  static function meta($post, $attr){
    return get_post_meta($post->ID, static::meta_key_for($attr), true);
  }

  static function meta_key_for($attr){
    return static::$post_type . "_" . $attr;
  }

  static function _before_save($data){
    if(static::$post_type !== $data["post_type"])return $data;
    foreach(static::$meta_box_classes as $attr=>$meta_box)
      $data= static::before_save($data, $attr, $_POST[static::meta_key_for($attr)]);
    return $data;
  }
  static function before_save($data, $attr){return $data;}

  static function _after_save($post_id, $post){
    if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return false;
    foreach(static::$meta_box_classes as $attr=>$meta_box){
      if(!$meta_box->is_authorized($post_id))return false;
      $meta_box->after_save($post);
      static::after_save($post, $attr, $_POST[static::meta_key_for($attr)]);
    }
  }
  //NOOP
  static function after_save($post, $attr){}

  static function add_post_type(){
    register_post_type(static::$post_type, static::post_type_options());
    static::add_rewrite_rules();
  }

  static function register_meta_boxes(){
    foreach(array_values(static::$meta_box_classes) as $meta_box)
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
