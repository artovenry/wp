<?
namespace Test;

class Event extends \Artovenry\Wp\CustomPost\Base{
  static $post_type= "event";
  static $post_type_options=[
    "label"=>"ライブ"
  ];
  static $meta_attrs= ["show_at_home", "scheduled_on"];
  static $meta_box_options=[
    "show_at_home"=> [
      "label"=> "トップページに表示"
    ],
    "scheduled_on"=> [
      "label"=> "日時"
    ]
  ];

}