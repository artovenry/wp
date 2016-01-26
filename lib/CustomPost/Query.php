<?
namespace Artovenry\Wp\CustomPost;

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