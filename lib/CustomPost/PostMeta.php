<?
namespace Artovenry\Wp\CustomPost;

trait PostMeta{
  function create_or_update_meta($attr, $value){
    update_post_meta($this->post_id, static::meta_key_for($attr), $value);
  }
  function delete_meta($attr){
    delete_post_meta($this->post_id, static::meta_key_for($attr));
  }
  function get_meta($attr){
    return get_post_meta($this->post_id, static::meta_key_for($attr), true);
  }

  function set_meta($attr, $value){
    $this->create_or_update_meta($attr, $value);
  }

  private static function meta_key_for($attr){
    return Constants::PREFIX . "_" . static::$post_type . "_" . $attr;
  }
}