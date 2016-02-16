<?
namespace Artovenry\Wp\CustomPost;

class Haml {
  static function initialize($viewpath){
    $viewpath= defined("ART_VIEW")? ART_VIEW . "/{$viewpath}" : $viewpath;
    \Artovenry\Haml::initialize($viewpath, ["helpers"=>[
      'Artovenry\\Wp\\CustomPost\\Helper'
    ]]);
  }
  static function render_box($template, $locals=[], $nonce_key, $nonce_name){
    echo wp_nonce_field($nonce_key, $nonce_name, true, false);
    \Artovenry\Haml::renderer()->render_template($template, $locals);
  }
}
