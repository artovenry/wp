<?
namespace Artovenry\Wp\CustomPost;
require_once "errors.php";

trait Query{
  static function find($id, $raise= true){
    if($post= get_post($id))
      return static::build($post);
    if($raise) throw new RecordNotFound;
    return false;
  }

  static function all($args=[]){
    $args= array_merge(["posts_per_page"=> -1],static::parse_query($args));
    return static::fetch($args);
  }

  static function where($args=[]){
    $posts= get_posts(static::parse_query($args));
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
      $args= array_merge(static::parse_query($limit_or_args), ["posts_per_page"=>1]);
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

  private static function parse_query($query){
    $defaults=[
      "post_type"=>static::post_type()
    ];
    $query= wp_parse_args($query, $defaults);
    return static::parse_meta_query($query);
  }

  private static function parse_meta_query($query){
    $convert= function($attr){
      if(!static::is_attr_defined($attr))return $attr;
      return static::meta_key_for($attr);
    };

    if(!empty($query["meta_key"]))
      $query["meta_key"]= $convert($query["meta_key"]);

    if(!empty($query["meta_query"])){
      if(!is_array($query["meta_query"]))return $query;
      $meta_query= &$query["meta_query"];

      array_walk_recursive($meta_query, function(&$item, $key) use($convert){
        if($key == "key")
          $item= $convert($item);
      });
    }

    return $query;
  }
}
