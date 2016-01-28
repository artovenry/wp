<?
namespace Artovenry\Wp\CustomPost;

//TODO: consider for various injection attacks!
//TODO: define collection_check_boxes
class FormHelper extends \Artovenry\ViewHelper{

/*
	static function apply($record, $name, $args){
		array_unshift($args, $record);
		$helpers= static::helpers();
		foreach($helpers as $key=>$helper)
			if($key === "_{$name}")
				return call_user_func_array($helper, $args);
	}
*/

	function radio_button($args){
		$attributes= call_user_func_array([$this,"attributes_for_check_fields"], func_get_args());
		$attributes= $this->element_attributes_for($attributes);		
		return sprintf('<input type="radio" %s />', join(" ", $attributes));
	}



	function check_box($args){
		$args= func_get_args();
		$record= array_shift($args);
		$attribute= array_shift($args);
		$unchecked_value= array_pop($args);
		$checked_value= array_pop($args);
		$options= array_pop($args);

		$attributes= call_user_func_array([$this,"attributes_for_check_fields"], func_get_args());
		$attributes= $this->element_attributes_for($attributes);
		$attributes["name"]= "{$attributes["name"]}[]";
		return sprintf('<input type="checkbox" %s />', join(" ", $attributes));
	}

	private function element_attributes_for($hash){
		$str=[];
		foreach($hash as $key=>$value){
			if($value === null)continue;
			$str[]= sprintf('%s="%s"', $key, (string)$value);
		}
		return $str;
	}

	private function attributes_for_check_fields($args){
		$args= func_get_args();
		list($record, $attribute, $value)= $args;
		$options= array_pop($args);

		$name= empty($options["name"])? "{$record->post_type}[{$attribute}]": $options["name"];
		$id= empty($options["id"])? "{$record->post_type}_{$attribute}_{$value}": $options["id"];
		$class= empty($options["class"])? null: $options["class"];
		$checked= ($record->$attribute === $value)? "checked": null;
		if(!empty($options["checked"]))$checked= $options["checked"];

		return compact( "name", "id", "value", "class", "checked");
	}
}
