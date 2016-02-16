<?
namespace Artovenry\Wp\CustomPost;

class CsrfAuthorization{
  const TOKEN_PREFIX= "_art_nonce";
  static function verify($value, $key){
    return wp_verify_nonce($value, $key);
  }
  static function token_for($name){
    return join("_", [self::TOKEN_PREFIX, $name]);
  }
}
