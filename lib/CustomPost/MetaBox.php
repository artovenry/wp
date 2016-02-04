<?
namespace Artovenry\Wp\CustomPost;

class MetaBox{
  static function init($class){
    if(empty($class::$meta_box_options))return [];
    $meta_boxes= [];
    foreach($class::$meta_box_options as $name=>$options)
      $meta_boxes[]= new self($class, $name, $options);
    return $meta_boxes;
  }
  private function __construct($class, $name, $options=[]){
    extract($class::$meta_box_options[$name]);
    $this->name= $name;
    $this->label= $label;
    $this->context= empty($context)? Constants::DEFAULT_META_BOX_CONTEXT: $context;
    $this->priority= empty($priority)? Constants::DEFAULT_META_BOX_PRIORITY: $priority;
    $this->post_type= $class::$post_type;
    $this->class= $class;
    $this->template= isset($template)? $template: null;
    $this->args= isset($options["args"])? (array)$options["args"]: [];
  }

  function render($post, $args=[]){
    \Artovenry\Haml::run($this->view_path(), ["helpers"=>["Artovenry\\Wp\\CustomPost\\FormHelper"]]);
    echo wp_nonce_field($this->nonce_key(),$this->name, true, false);
    $class= $this->class;
    $post= $class::build($post);
    $post_type= $this->post_type;

    $args=array_merge([
      "post_type"=>$post_type,
      $post_type=>$post,
    ], $args);
    render_template($this->template(), $args);
  }

  function register(){
    add_meta_box($this->prefixed_name(), $this->label, [$this, "render"], get_current_screen(), $this->context, $this->priority, $this->args);
  }

  private function prefixed_name(){
    $class= $this->class;
    $post_type= $this->post_type;
    return join("_", [Constants::PREFIX, $class::$post_type, $this->name]);
  }

  private function template(){
    if(empty($this->template))
      return $this->post_type . "/" . $this->name;
    return $this->template;
  }
  private function view_path(){
    return defined("ART_VIEW")? ART_VIEW . "/meta_boxes" : "meta_boxes";
  }

  function nonce_key(){
    return "_nonce_" . $this->prefixed_name();
  }
}