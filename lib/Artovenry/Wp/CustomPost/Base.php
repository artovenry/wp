<?
namespace Artovenry\Wp\CustomPost;
require_once "errors.php";

abstract class Base{
  use Query;
  use PostMeta;
  const DEFAULT_META_PREFIX= ART_PREFIX;
  const DEFAULT_POST_TYPE_OPTIONS=[
    "public"=>true,
    "supports"=>["title", "editor", "author", "thumbnail",  "excerpt",  "revisions"],
    "hierarchical"=>false,
    "rewrite"=>false
  ];
  static function initialize(){
    new Initializer(get_called_class());
  }
  static function build($post_or_post_id){
    return new static($post_or_post_id);
  }
  static function meta_prefix(){
    if(empty($opt= static::options_for("post_type_options")))
      return self::DEFAULT_META_PREFIX;
    if(empty($opt["meta_prefix"]))
      return self::DEFAULT_META_PREFIX;
    return $opt["meta_prefix"];
  }
  static function is_attr_defined($attr, $raise= false){
    $attributes= static::options_for("meta_attributes");
    foreach($attributes as $item)
      if($item === $attr)return true;
    if($raise)throw new AttributeNotDefined($attr);
    return false;
  }
  static function post_type(){
    return static::options_for("post_type_options")["name"];
  }
  static function options_for($name){
      if(isset(static::$$name))
        return (array) static::$$name;
      $method= join("::", [get_called_class(), $name]);
      if(is_callable($method))
        return (array)call_user_func($method);
      return [];
  }
  function __get($name){
    if($name==="post")return $this->post;
    if($name==="post_id")return $this->post_id;
    foreach(self::options_for("meta_attributes") as $attr)
      if($name === $attr)return $this->get_meta($attr);
    return $this->post->$name;
  }
  function is_auto_draft(){
    return $this->post->post_status === "auto-draft";
  }
  function to_a(){
    $post= $this->post->to_array();
    $post_meta= array_reduce(static::options_for("meta_attributes"), function($rs, $item){
      $rs[$item]= $this->get_meta($item);
      return $rs;
    },[]);
    return array_merge($post, $post_meta);
  }
  function to_array(){return $this->to_a();}


  //private
    private function __construct($post_or_post_id){
      $p= $post_or_post_id;
      $this->post= is_int($p)? get_post($p): $p;
      $this->post_id= is_int($p)? $p: $p->ID;
    }
}
