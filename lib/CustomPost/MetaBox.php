<?
namespace Artovenry\Wp\CustomPost;

class MetaBox{
  static function init($class){
    if(empty($class::$meta_box_options))return [];
    $meta_boxes= [];
    foreach($class::$meta_box_options as $attr=>$options)
      $meta_boxes[]= new self($class, $attr, $options);
    return $meta_boxes;
  }
  private function __construct($class, $attr, $options=[]){
    extract($class::$meta_box_options[$attr]);
    $this->name= join("_", [Constants::PREFIX, $class::$post_type, $attr]);
    $this->label= $label;
    $this->context= empty($context)? Constants::DEFAULT_META_BOX_CONTEXT: $context;
    $this->priority= empty($priority)? Constants::DEFAULT_META_BOX_PRIORITY: $priority;
    $this->attribute= $attr;
    $this->post_type= $class::$post_type;
    $this->class= $class;
    $this->template= isset($template)? $template: null;
  }

  function render($post, $args=[]){
    \Artovenry\Haml::run($this->view_path());
    extract(Helper::helpers());
    echo $_nonce_field_for($this->name, $this->nonce_key());
    $class= $this->class;
    $post= $class::build($post);
    $post_type= $this->post_type;
    $attr= $this->attribute;
    $value= $post->$attr;
    $args=array_merge([
      "post_type"=>$post_type,
      $post_type=>$post,
      $attr=>$value,
      //"name"=>$this->name
      // eg: name="event[show_on_top]"
      "name"=>"{$post_type}[{$attr}]"
    ], $args);
    render_template($this->template(), $args);
  }

  function register(){
    add_meta_box($this->name, $this->label, [$this, "render"], get_current_screen(), $this->context, $this->priority);
  }

  function after_save($post_id, $post, $updated){
    if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return false;
    if(!current_user_can("edit_post", $post_id)) return false;
    if(!wp_verify_nonce($_POST[$this->nonce_key()], $this->name)) return false;
    $class= $this->class;
    $posted_value= $_POST[$post->post_type][$this->attribute];
    if(!empty($value= $posted_value))
      $class::build($post)->set_meta($this->attribute, $value);
    else
      $class::build($post)->delete_meta($this->attribute);
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
    return "_nonce_" . $this->name;
  }
}