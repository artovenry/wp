<?
namespace Artovenry\Wp\PostType;

trait MetaMethods{
  function create_or_update_meta($post, $value){
    update_post_meta($post->ID, $this->meta_key(), $value);
  }
  function delete_meta($post){
    delete_post_meta($post->ID, $this->meta_key());
  }
  function get_meta($post){
    return get_post_meta($post->ID, $this->meta_key(), true);
  }

  function after_save($post){
    $posted_value = $_POST[$this->meta_key()];
    if(!empty($posted_value))
      $this->create_or_update_meta($post, $posted_value);
    else
      $this->delete_meta($post);
  }

  function is_authorized($post_id){
    if(!wp_verify_nonce( $_POST[$this->nonce_key()], MetaBox::IDENTIFIER))
      return false;
    if(!current_user_can('edit_post', $post_id))
      return false;
    return true;
  }

}