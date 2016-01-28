<?
namespace Test;

class Event extends \Artovenry\Wp\CustomPost\Base{
  static $post_type= "event";
  static $post_type_options=[
    "label"=>"ライブ"
  ];
  static $meta_attrs= ["show_at_home", "scheduled_on", "hoge"];
  static $meta_box_options=[
    "show_at_home"=> [
      "label"=> "トップページに表示",
      "template"=>"show_at_home"
    ],
    "scheduled_on"=> [
      "label"=> "日時"
    ],
    "hoge"=>[
      "label"=>"hhhh",
      "template"=>"boge"
    ]
  ];

  //static function after_save($post_id, $post, $updated){
  //}
}