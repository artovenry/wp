<?
namespace Artovenry\Wp\PostType;

trait Rewrite{
  static function add_rewrite_rules(){
    $post_type= static::$post_type;
    add_rewrite_tag( "%$post_type%", '([^/]+)', "post_type=$post_type&p=");
    add_rewrite_rule("{$post_type}/?$", 'index.php?post_type=' . $post_type);
    add_rewrite_rule("{$post_type}/(\d+)/?$", 'index.php?p=$matches[1]&post_type=' . $post_type);
  }

  private static function is_published($post){
    $status= get_post_status($post->ID);
    return in_array($status, ['draft', 'pending', 'auto-draft', 'future'])? false: true;
  }

  static function permalink($url, $post){
    $post_type= static::$post_type;
    if($post->post_type !== $post_type)return $url;
    if(!self::is_published($post))
      return home_url(add_query_arg([
        "post_type"=>$post_type,
        "p"=>$post->ID
      ], ""));
    return home_url("{$post_type}/{$post->ID}");
  }

}