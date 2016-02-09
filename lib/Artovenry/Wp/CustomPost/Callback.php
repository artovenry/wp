<?
namespace Artovenry\Wp\CustomPost;
require_once __DIR__ . "/Error.php";

trait Callback{
  static function register_callbacks($meta_boxes){
    add_action("save_post_" . static::$post_type, function($post_id, $post, $updated) use($meta_boxes){
      if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return false;
      if(!empty($_POST)){
        try{
          static::authorize($post_id, $meta_boxes);
        }catch(RequestNotAuthenticated $e){
          if(defined("ART_ENV") and (ART_ENV === "development"))throw $e;
          return false;
        }

        $post= static::build($post);
        if($post->is_auto_draft())return;
        $params= $_POST[static::$post_type];
        if(!isset(static::$meta_attrs) or !is_array(static::$meta_attrs))return;
        foreach(static::$meta_attrs as $attr){
          if(empty($params[$attr]))
            $post->delete_meta($attr);
          else
            $post->set_meta($attr, $params[$attr]);
        }
      }
      static::after_save($post);
    }, 10, 3);
    add_filter("wp_insert_post_data", function($data, $postarr){
      if(static::$post_type !== $data["post_type"])return $data;
      return static::before_save($data, $postarr);
    },10,2);
  }

  static function authorize($post_id, $meta_boxes){
    if(!current_user_can("edit_post", $post_id)) throw new RequestNotAuthenticated;
    if(!is_user_logged_in()) throw new RequestNotAuthenticated;
    foreach($meta_boxes as $item){
      if(!wp_verify_nonce($_POST[$item->name], $item->nonce_key())) throw new RequestNotAuthenticated;
    }
  }

  static function after_save($post){}//NOOP
  static function before_save($data, $postarr){return $data;}//NOOP
}
