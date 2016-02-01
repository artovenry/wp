<?
namespace Artovenry\Wp\CustomPost;
class FormHelper extends \Artovenry\ViewHelper{
	function radio_button($args){
		$attributes= call_user_func_array([$this,"attributes_for_check_fields"], func_get_args());
		$attributes= $this->element_attributes_for($attributes);
		return sprintf('<input type="radio" %s />', join(" ", $attributes));
	}
	function check_box($args){
		$default_checked_value= "1";
		$default_unchecked_value= "0";
		$args= func_get_args();
		$record= array_shift($args);
		$attribute= array_shift($args);
		$unchecked_value= array_pop($args);
		$checked_value= array_pop($args);
		$options= (array)array_pop($args);

		$value_for_check_box= isset($checked_value)? $checked_value: "1";
		$value_for_hidden= isset($unchecked_value)? $unchecked_value: "0";

		$attributes= call_user_func_array([$this,"attributes_for_check_fields"], [$record, $attribute, $value_for_check_box, $options]);
		if(empty($options["id"]))
			$attributes["id"]= "{$record->post_type}_{$attribute}";

		$attributes= $this->element_attributes_for($attributes);
		$check_box= sprintf('<input type="checkbox" %s />', join(" ", $attributes));

		$name= empty($options["name"])? "{$record->post_type}[{$attribute}]": $options["name"];
		$hidden= sprintf('<input type="hidden" name="%s" value="%s" />', $name , $value_for_hidden);
		return $hidden . $check_box;
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
