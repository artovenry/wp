<?
namespace Test;

class Blog extends \Artovenry\Wp\CustomPost\Base{
  static $post_type_options=[
    "name"=>    "blog",
    "label"=>   "ブログ",
  ];

  static $meta_attributes= ["attr_a", "attr_b", "attr_c"];

  static function meta_boxes(){
    return [
      [
        "name"=>    "box_a",
        "label"=>   "BOX_A",
      ],
      [
        "name"=>    "box_b",
        "label"=>   "BOX_B",
      ],
    ];
  }

}