<?
namespace Test;

class Blog extends \Artovenry\Wp\CustomPost\Base{
  static $post_type= "blog";
  static $post_type_options=[
    "label"=>"ぶろぐ"
  ];
  static $meta_attrs= ["attr_a", "attr_b", "attr_c"];

  static function meta_box_options(){
    return [
      "box_a"=>["label"=>"BOX_A"],
      "box_b"=>["label"=>"BOX_B"],
    ];
  }

}