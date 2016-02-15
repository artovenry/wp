<?
namespace Artovenry\Wp\CustomPost;

class Haml extends \Artovenry\Haml{
  static function initialize($viewpath){
    $viewpath= defined("ART_VIEW")? ART_VIEW . "/{$viewpath}" : $viewpath;
    parent::initialize($viewpath, ["helpers"=>[
      'Artovenry\\Wp\\CustomPost\\Helper'
    ]]);
  }
  static function render_box($template, $locals=[], $context, $name){
    echo wp_nonce_field($context, $name, true, false);
    static::render_template($template, $locals);
  }
}
