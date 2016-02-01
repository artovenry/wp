<?
namespace Test;

class Event extends \Artovenry\Wp\CustomPost\Base{
  static $post_type= "event";
  static $post_type_options=[
    "label"=>"ライブ"
  ];
  static $meta_attrs= ["show_at_home", "scheduled_on", "hoge"];
  static $meta_box_options=[
    "option"=>[
      "label"=>"設定",
    ],
    "hoge"=>[
      "label"=>"hhhh",
      "template"=>"boge"
    ]
  ];
}