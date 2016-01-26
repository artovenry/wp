<?
namespace Artovenry\Wp\CustomPost;

abstract class Constants{
  const PREFIX= "art";
  const DEFAULT_POST_TYPE_OPTIONS=[
    "public"=>true,
    "supports"=>["title", "editor", "author", "thumbnail",  "excerpt",  "revisions"],
    "hierarchical"=>false,
    "rewrite"=>false
  ];
}

trait PostMeta{
  function create_or_update_meta($attr, $value){
    update_post_meta($this->post_id, $this->meta_key(), $value);
  }
  function delete_meta($attr){
    delete_post_meta($post->ID, $this->meta_key());
  }
  function get_meta($attr){
    return get_post_meta($this->post_id, $this->meta_key(), true);
  }
}

class Meta{
  private $post;

  function read(){
    return get_post_meta($this->$post->ID, $this->meta_key(), true);
  }
  function meta_key(){
  }
}

trait Callback{
  static function after_save($post_id, $post, $updated){}//NOOP

  static function _before_save($data, $postarr){
    if(static::$post_type !== $data["post_type"])return $data;
    return static::before_save($data);
  }
  static function before_save($data){return $data;}//NOOP  
}
trait Query{
  static function build($post_or_post_id){
    return new static($post_or_post_id);
  }
  static function where($args=[]){
    foreach(get_posts(static::parse_args($args)) as $item)
      yield static::build($item);
  }
  static function take($args=[]){
    $post= static::fetch(array_merge(static::parse_args($args),["posts_per_page"=>1]));
    return array_shift($post);
  }
  static function fetch($args=[]){
    $rs=[];
    foreach(static::where($args) as $item)
      $rs[]= $item;
    return $rs;
  }
  private static  function parse_args($args){
    return wp_parse_args($args,[
      "post_type"=>static::$post_type
    ]);
  }
}

abstract class Base{
  use Callback;
  use Query;
  private $post;
  private $post_id;

  function __get($name){
    if($name==="post")return $this->post;
    if($name==="post_id")return $this->post_id;
    return $this->post->$name;
  }
  static function register(){
    register_post_type(static::$post_type, static::post_type_options());
    add_action("save_post_" . static::$post_type, get_called_class() . "::after_save", 10, 3);
    add_filter("wp_insert_post_data", get_called_class() . "::_before_save", 10, 2);
  }

  
  private static function post_type_options(){
    $options= static::$post_type_options;

    $options["register_meta_box_cb"]= function(){
    };

    if(empty($options["label"]))
      $options["label"]= static::$post_type;

    return array_merge(Constants::DEFAULT_POST_TYPE_OPTIONS, $options);
  }
  private function __construct($post_or_post_id){
    $this->post= is_int($post_or_post_id)? get_post($post_or_post_id): $post_or_post_id;
    $this->post_id= is_int($post_or_post_id)? $post_or_post_id: $post_or_post_id->ID;
    foreach(static::$meta_attrs as $attr){
    }
  }

}