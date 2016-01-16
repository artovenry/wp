<?
namespace Artovenry\Wp\PostType;

class Helper extends \Artovenry\ViewHelper{
  function nonce_field_for($key){
    return wp_nonce_field(MetaBox::IDENTIFIER, $key, true, false);
  }
}