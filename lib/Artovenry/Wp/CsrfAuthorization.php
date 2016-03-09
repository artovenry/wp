<?
namespace Artovenry\Wp;

class CsrfAuthorization{
  const TOKEN_PREFIX= "_" . ART_PRERFIX . "_nonce";
  static function verify($value, $key){
    return wp_verify_nonce($value, $key);
  }
  static function token_for($name){
    return join("_", [self::TOKEN_PREFIX, $name]);
  }
}
