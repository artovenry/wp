<?
namespace Artovenry\Wp\CustomPost;
class MetaBox extends \Artovenry\Wp\AbstractMetaBox{
  const CONTEXT= "side";
  const PRIORITY= "core";
  const VIEWPATH= "meta_boxes";
  protected $post_type_class;
  protected $post_type;

  function __construct($post_type_class, $options){
    $this->post_type_class= $post_type_class;
    $this->post_type= $post_type_class::post_type();
    $name= join("_", [$this->post_type, $options["name"]]);
    if(!isset($options["template"]))
      $options["template"]=  join("/", [$this->post_type, $options["name"]]);
    $options= array_merge([
      "context"=> self::CONTEXT,
      "priority"=> self::PRIORITY,
      "args"=> [],
    ], $options);
    parent::__construct($name, $options);
  }

  function register(){    
    extract($this->options);
    add_meta_box($this->prefixed_name, $label,[$this, "render"], get_current_screen(), $context, $priority, $args);
  }

  function render($post, $args){
    extract($this->options);
    $class= $this->post_type_class;
    $post_type= $this->post_type;
    $locals= array_merge([
      "post_type"=> $post_type,
      $post_type=> $class::build($post),
    ], $args);
    Haml::render_box($template, $locals, $this->nonce_key(),$this->nonce_name());
  }
}