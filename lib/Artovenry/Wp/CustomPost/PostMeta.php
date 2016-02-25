<?
namespace Artovenry\Wp\CustomPost;
require_once "errors.php";

trait PostMeta{
  function create_or_update_meta($attr, $value){
    if(!is_bool($value) AND !is_string($value) AND !is_numeric($value))
      throw new TypeIsNotScalar;
    $value= (string) $value;
    if(static::is_attr_defined($attr, true))
      update_post_meta($this->post_id, static::meta_key_for($attr), $value);
  }

  function delete_meta($attr){
    if(static::is_attr_defined($attr, true))
      delete_post_meta($this->post_id, static::meta_key_for($attr));
  }
  function get_meta($attr){
    if(static::is_attr_defined($attr, true)){
      $value= get_post_meta($this->post_id, static::meta_key_for($attr), true);
      if($value === "")return null;
      return $value;
    }
  }
  function set_meta(){
    $args= func_get_args();
    $attr_or_hash= array_shift($args);
    if(is_string($attr_or_hash))
      $this->create_or_update_meta($attr_or_hash, array_shift($args));
    else
      foreach($attr_or_hash as $attr=>$value)
        $this->create_or_update_meta($attr, $value);
  }

  static function meta_key_for($attr_name){
    return join("_", [static::meta_prefix(), static::post_type(), $attr_name]);
  }
}