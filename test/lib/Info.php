<?
namespace Test;

class Info extends \Artovenry\Wp\CustomPost\Base{
  static $post_type_options=[
    "name"=>    "info",
    "label"=>   "お知らせ",
  ];
  static $meta_attributes= ["show_at_home", "oshirase"];
  static $meta_boxes=[
    [
      "name"=>      "show_at_home",
      "label"=>     "トップに表示",
      "template"=>  "show_at_home"
    ],
  ];
}