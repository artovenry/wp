<?
namespace Artovenry\Wp\CustomPost;

class MetaBox{
  static function init($class){
    foreach($class::$meta_box_options as $attr=>$options){
      $meta_box= new self($class, $attr, $options);
      $meta_box->register();
    }
  }
  private function __construct($class, $attr, $options=[]){
    extract($class::$meta_box_options[$attr]);
    $this->id= join("_", [Constants::PREFIX, $class::$post_type, $attr]);
    $this->label= $label;
    $this->context= empty($context)? Constants::DEFAULT_META_BOX_CONTEXT: $context;
    $this->priority= empty($priority)? Constants::DEFAULT_META_BOX_PRIORITY: $priority;
    $this->attribute= $attr;
    $this->post_type= $class::$post_type;
    $this->class= $class;
    $this->template= $template;
  }

  function render($post, $args=[]){
    \Artovenry\Haml::run($this->view_path());
    extract(Helper::helpers());
    echo $_nonce_field_for($this->id, $this->nonce_key());
    $class= $this->class;
    $post= $class::build($post);
    $post_type= $this->post_type;
    $attr= $this->attribute;
    $value= $post->$attr;
    $args=array_merge([
      $post_type=>$post,
      $attr=>$value
    ], $args);
    render_template($this->template(), $args);
  }

  function register(){
    add_meta_box($this->id, $this->label, [$this, "render"], get_current_screen(), $this->context, $this->priority);
  }

  private function template(){
    if(empty($this->template))
      return $this->post_type . "/" . $this->attribute;
    return $this->template;
  }
  private function view_path(){
    return defined("ART_VIEW")? ART_VIEW . "/meta_boxes" : "meta_boxes";
  }
  private function nonce_key(){
    return "_nonce_" . $this->id;
  }
}