<?
namespace Artovenry\Wp\PostType;

class MetaBox{
  const IDENTIFIER= "art_meta";
  const DEFAULT_PRIORITY= "core";
  const DEFAULT_CONTEXT= "side";

  use MetaMethods;

  private $post_type;
  private $attribute;
  //private $priority;
  //private $context;
  //private $label;
  private $options;

  //private $meta_key;

  function __construct($post_type, $attribute, $options){
    $this->post_type= $post_type;
    $this->attribute= $attribute;
    $this->options= array_merge([
      "priority"=>self::DEFAULT_PRIORITY,
      "context"=>self::DEFAULT_CONTEXT
    ],$options);
  }
  /*
  function __construct($post_type, $attribute, $label, $priority , $context){
    $this->post_type= $post_type;
    $this->attribute= $attribute;
    $this->label= $label;
    $this->priority= $priority? $priority: self::DEFAULT_PRIORITY;
    $this->context= $context? $context: self::DEFAULT_CONTEXT;
    $this->meta_key= "{$post_type}_{$attribute}";
  }
  */

  function meta_key(){
    return $this->post_type . "_" . $this->attribute;
  }
  function view_path(){
    return defined("ART_VIEW")? ART_VIEW . "/meta_boxes" : "meta_boxes";
  }
  function template(){
    return $this->post_type . "/" . $this->attribute;
  }
  function nonce_key(){
    return "_nonce_" . $this->meta_key();
  }

  function render($post,$args){
    \Artovenry\Haml::run($this->view_path());
    extract(Helper::helpers());
    echo $_nonce_field_for($this->nonce_key());
    render_template($this->template(),[
      "post"=>$post,
      "meta_key"=>$this->meta_key(),
      "meta_value"=>$this->get_meta($post)
    ]);
  }

  function register(){
    $id= join("_", [self::IDENTIFIER, $this->meta_key()]); 

    add_meta_box($id, $this->options["label"], [$this, "render"],get_current_screen(), $this->options["context"], $this->options["priority"]);
  }
}

