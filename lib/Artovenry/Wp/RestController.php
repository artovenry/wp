<?
namespace Artovenry\Wp;
abstract class RestController{
	function render($arg=[]){
		return new \WP_REST_Response((new Conversion($arg))->to_a());
	}
}
