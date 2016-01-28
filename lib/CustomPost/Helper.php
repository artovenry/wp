<?
namespace Artovenry\Wp\CustomPost;

class Helper extends \Artovenry\ViewHelper{
	function nonce_field_for($action, $key){
		return wp_nonce_field($action,$key, true, false);
	}
}
