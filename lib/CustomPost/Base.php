<?
namespace Artovenry\Wp\CustomPost;

abstract class Base{
  use Callback;
  use Query;
  use PostMeta;
  private $post;
  private $post_id;

  function __get($name){
    if($name==="post")return $this->post;
    if($name==="post_id")return $this->post_id;
    foreach(static::$meta_attrs as $attr)
      if($name === $attr)return $this->get_meta($attr);
    return $this->post->$name;
  }
  static function register(){
    register_post_type(static::$post_type, static::post_type_options());
    add_action("save_post_" . static::$post_type, get_called_class() . "::_after_save", 10, 3);
    add_filter("wp_insert_post_data", get_called_class() . "::_before_save", 10, 2);
  }
  function set_meta($attr, $value){
    $this->create_or_update_meta($attr, $value);
  }
  function delete_meta($attr){
    $this->delete_meta($attr);
  }
  private static function post_type_options(){
    $options= static::$post_type_options;
    $options["register_meta_box_cb"]= function(){
      MetaBox::init(get_called_class());
    };
    if(empty($options["label"]))
      $options["label"]= static::$post_type;
    return array_merge(Constants::DEFAULT_POST_TYPE_OPTIONS, $options);
  }
  private function __construct($post_or_post_id){
    $this->post= is_int($post_or_post_id)? get_post($post_or_post_id): $post_or_post_id;
    $this->post_id= is_int($post_or_post_id)? $post_or_post_id: $post_or_post_id->ID;
  }

}