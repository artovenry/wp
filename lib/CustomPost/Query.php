<?
namespace Artovenry\Wp\CustomPost;

trait Query{
  static function all($args=[]){
    $args= array_merge(["posts_per_page"=> -1],static::parse_args($args));
    return static::fetch($args);
  }

  static function where($args=[]){
    $posts= get_posts(static::parse_args($args));
    foreach($posts as $item)
      yield static::build($item);
  }

  /*
  take()
  take(5)
  take("order=ASC")
  take(5,["order"=>"ASC"])
  */
  static function take($limit_or_args=1, $args=[]){
    if(!is_int($limit_or_args)){
      $args= array_merge(static::parse_args($limit_or_args), ["posts_per_page"=>1]);
    }else{
      $args= array_merge($args,["posts_per_page"=>$limit_or_args]);
    }

    $posts= static::fetch($args);
    return count($posts)===1? array_shift($posts): $posts;
  }
  static function fetch($args=[]){
    $rs=[];
    foreach(static::where($args) as $item)
      $rs[]= $item;
    return $rs;
  }
  private static function parse_args($args){
    $args= wp_parse_args($args,[
      "post_type"=>static::$post_type
    ]);
    $args= static::build_orderby_query($args);
    $rs=[];
    foreach($args as $key=>$value){
      if(!static::is_attr_defined($key)){
        $rs[$key]= $value;
      }else{
        $meta= [
          "key"=>static::meta_key_for($key),
          "value"=>$value
        ];
      }
    }
    if(!empty($meta)){
      $rs["meta_key"]= $meta["key"];
      $rs["meta_value"]= $meta["value"];    
    }
    return $rs;
  }

  private static function build_orderby_query($args){
    if(empty($args["orderby"]))return $args;
    $orderby= $args["orderby"];
    if(!is_string($orderby))return $args;
    if(!static::is_attr_defined($orderby))return $args;
    $args["orderby"]= "meta_value";
    $args["meta_key"]= static::meta_key_for($orderby);
    return $args;
  }
}