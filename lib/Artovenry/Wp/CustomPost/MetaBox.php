<?
namespace Artovenry\Wp\CustomPost;

class MetaBox{
  const CONTEXT= "side";
  const PRIORITY= "core";
  const PREFIX= "art";
  const VIEWPATH= "meta_boxes";

  private $post_type_class;
  private $post_type;
  private $prefixed_name;
  private $options;

  function __construct($post_type_class, $options){
    $this->post_type_class= $post_type_class;
    $this->post_type= $post_type_class::post_type();
    $this->prefixed_name= join("_", [self::PREFIX, $this->post_type, $options["name"]); 
    if(!isset($options["label"]))
      $options["label"]= $options["name"];
    if(isset($options["args"]))
      $options["args"]= (array)$options["args"];
    if(!isset($options["template"]))

    $this->options= array_merge([
      "context"=> self::CONTEXT,
      "priority"=> self::PRIORITY,
      "args"=> [],
      "template"=> join("/", [$this->post_type, $this->name]),
    ], $options);

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
    Haml::render_box($template, $locals, $this->prefixed_name, $this->name);
  }
}