<?
namespace Artovenry\Wp\CustomPost;

abstract class Constants{
  const DEFAULT_POST_TYPE_OPTIONS=[
    "public"=>true,
    "supports"=>["title", "editor", "author", "thumbnail",  "excerpt",  "revisions"],
    "hierarchical"=>false,
    "rewrite"=>false
  ];
}

abstract class Base{
  static function register(){
    return register_post_type(static::$post_type, static::post_type_options());
  }
  private static function post_type_options(){
    $options= static::$post_type_options;

    //FIXME
    $options["register_meta_box_cb"]= function(){};

    if(empty($options["label"]))
      $options["label"]= static::$post_type;

    return array_merge(Constants::DEFAULT_POST_TYPE_OPTIONS, $options);
  }
}