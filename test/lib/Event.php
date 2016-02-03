<?
namespace Test;

class Event extends \Artovenry\Wp\CustomPost\Base{
  static $post_type= "event";
  static $post_type_options=[
    "label"=>"ライブ",
    "taxonomies"=>["products"]
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


  static function register_post_type($meta_boxes){
    parent::register_post_type($meta_boxes);
    $rs= register_taxonomy('products', 'event', [
      'hierarchical' => true, 
      'label' => '製品のカテゴリー',
      'singular_label' => '製品のカテゴリー',
      'public' => true,
      "show_admin_column"=>true,
      'show_ui' => true
    ]);
  }
}