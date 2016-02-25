<?
namespace Artovenry\Wp\Support;
use \WP_Post as WP_Post;
use \Artovenry\Wp\CustomPost;

class Conversion{
	private $data=[];
	function __construct($arg){
		if(!is_array($arg)){
			$this->convert($arg);
			$this->data= $arg;
		}else{
			array_walk_recursive($arg, function(&$item){
				$this->convert($item);
			});
			$this->data= $arg;
		}
	}
	function to_a(){
		return $this->data;
	}

	//private
	private function convert(&$arg){
		if(($arg instanceof WP_Post) or ($arg instanceof CustomPost\Base)){
			$arg= $arg->to_array();
		}else{
			$arg= (array)$arg;
		}
	}
}
