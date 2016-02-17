<?
namespace Test;

class Event extends \Artovenry\Wp\CustomPost\Base{
  //hash or function or string
  static function post_type_options(){
    return [
      "name"=>    "event",
      "label"=>   "ライブ",
    ];
  }

  //array or function
  static $meta_attributes= ["show_at_home", "scheduled_on", "hoge"];

  //array or function
  static $meta_boxes=[
    [
      "name"=>    "option",
      "label"=>   "設定",
    ],
    [
      "name"=>    "hoge",
      "template"=>   "boge"
    ],
  ];

  //hash or function
  static function posts_list_options(){
    return[
      "order"=>["show_at_home", "title","taxonomy-products", "scheduled_on", "date", "some_column"],
      "columns"=>[
        "show_at_home"=>["label"=>""],
        "scheduled_on"=>["label"=>"開催日",
          "render"=>function($record){echo "<b style='color:red;'>{$record->scheduled_on}</b>";},
        ],
        "some_column"=>["label"=>"SOME COLUMN"]
      ]
    ];
  }
}