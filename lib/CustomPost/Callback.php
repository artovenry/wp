<?
namespace Artovenry\Wp\CustomPost;

trait Callback{
  static function after_save($post_id, $post, $updated){}//NOOP
  static function _after_save($post_id, $post, $updated){
    if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return false;

  }

  static function _before_save($data, $postarr){
    if(static::$post_type !== $data["post_type"])return $data;
    return static::before_save($data);
  }
  static function before_save($data){return $data;}//NOOP  
}