<?
namespace Artovenry\Wp\CustomPost;

class PostsListTable{
	const BUILT_IN_COLUMNS=[
		"date", "title", "comments", "author"
	];
	private $post_type_class;
	private $post_type;
	private $columns;
	private $order;

	function __construct($post_type_class){
		$this->post_type_class= $post_type_class;
		$this->post_type= $post_type_class::post_type();
		$options= $post_type_class::options_for("posts_list_options");
		if(isset($options["columns"]))
			$this->columns=  $options["columns"];
		if(isset($options["order"]))
			$this->order= $options["order"];
	}

	function register_columns($default_columns){
		if(empty($columns= $this->build_columns()))return $default_columns;
		foreach($default_columns as $key=>$value)
			if(array_key_exists($key, $columns))
				$columns[$key]= $value;
		return $columns;
	}
	function render($column_name, $post_id){
		$option= $this->columns[$column_name];
		$class= $this->post_type_class;
		$record= $class::find($post_id);
		if(isset($option["render"]) AND is_callable($option["render"]))
			return $option["render"]($record);
		if($class::is_attr_defined($column_name))
			echo $record->get_meta($column_name);
	}

	//private 
		private function column_names(){
			return array_keys($this->columns);
		}
		private function build_columns(){
			if(empty($this->order))return false;
			$columns= array_filter($this->order, function($item){
				return in_array($item, array_merge($this->built_in_columns(), $this->column_names()));
			});
			array_unshift($columns, "cb");
			$rs=[];
			foreach($columns as $column)
				if(array_key_exists($column, $this->columns))
					$rs[$column]= $this->columns[$column]["label"];
				else
					$rs[$column]= null;
			return $rs;
		}
		private function built_in_columns(){
			$columns= array_filter(self::BUILT_IN_COLUMNS, function($item){
				if($item=="title")
					return post_type_supports($this->post_type, "title");
				if($item=="comments")
					return post_type_supports($this->post_type, "comments");
				if($item=="author")
					return post_type_supports($this->post_type, "author");
				return true;
			});
			$tax_columns= array_map(function($item){
				return "taxonomy-{$item}";
			}, get_object_taxonomies($this->post_type));
			return array_merge($columns, $tax_columns);
		}
}