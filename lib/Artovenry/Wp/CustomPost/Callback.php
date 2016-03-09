<?
namespace Artovenry\Wp\CustomPost;
use Artovenry\Wp\CsrfAuthorization;
require_once "errors.php";

class Callback{
  private $post_type_class;
  private $meta_boxes;
  function __construct($post_type_class, $meta_boxes=[]){
    $this->post_type_class= $post_type_class;
    $this->meta_boxes= $meta_boxes;
  }
  function after_save($post_id, $post, $updated){
    if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return false;
    if(!empty($_POST)){
      try{
        $this->authorize($post_id);
      }catch(RequestNotAuthenticated $e){
        if(defined("ART_ENV") and (ART_ENV === "development"))throw $e;
        return false;
      }
      $this->persist_meta($post);
    }
  }

  //private
    private function authorize($post_id){
      if(!current_user_can("edit_post", $post_id)) throw new RequestNotAuthenticated;
      if(!is_user_logged_in()) throw new RequestNotAuthenticated;
      $class= $this->post_type_class;
      foreach($this->meta_boxes as $item){
        $key= $item->nonce_key();
        $value= $_POST[$item->nonce_name()];
        if(!CsrfAuthorization::verify($value, $key)) throw new RequestNotAuthenticated;
      }
    }
    private function persist_meta($post){
      $class= $this->post_type_class;
      $post= $class::build($post);
      $post_type= $class::post_type();
      if($post->is_auto_draft())return;
      $params= $_POST[$post_type];
      foreach($class::options_for("meta_attributes") as $attr){
        if(empty($params[$attr]))
          $post->delete_meta($attr);
        else
          $post->set_meta($attr, $params[$attr]);
      }
    }

}