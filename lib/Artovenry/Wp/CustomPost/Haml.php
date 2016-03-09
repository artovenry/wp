<?
namespace Artovenry\Wp\CustomPost;
class Haml extends \Artovenry\Haml{
  static function initialize($viewpath){
    if(defined("ART_VIEW"))
      $viewpath= join("/", [ART_VIEW, $viewpath]);
    parent::initialize($viewpath,["helpers"=>[
      'Artovenry\\Wp\\CustomPost\\Helper'
    ]]);
  }
  static function render_box($template, $locals=[], $nonce_key, $nonce_name){
    echo wp_nonce_field($nonce_key, $nonce_name, true, false);
    parent::renderer()->render_template($template, $locals);
  }
}
