<?
namespace Test;

class Info extends \Artovenry\Wp\CustomPost\Base{
  static $post_type= "info";
  static $post_type_options=[
    "label"=>"お知らせ"
  ];
  static $meta_attrs= ["show_at_home", "oshirase"];
  static $meta_box_options=[
    "show_at_home"=>["label"=> "トップに表示", "template"=>"show_at_home"]
  ];
}