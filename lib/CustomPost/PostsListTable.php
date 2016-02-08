<?
namespace Artovenry\Wp\CustomPost;

class PostsListTable{
	const BUILT_IN_COLUMNS=[
		"date", "title", "comments", "author"
	];
	private $class;
	private $post_type;
	private $columns;
	private $order;

	function columns(){
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

	function column_names(){
		return array_keys($this->columns);
	}

	function built_in_columns(){
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

	function register_columns($default_columns){
		if(empty($columns= $this->columns()))return $default_columns;
		foreach($default_columns as $key=>$value)
			if(array_key_exists($key, $columns))
				$columns[$key]= $value;
		return $columns;
	}

	function register(){
		add_filter("manage_edit-{$this->post_type}_columns",[$this, "register_columns"]);
		add_action("manage_{$this->post_type}_posts_custom_column", [$this, "render"], 10, 2);
	}

	function render($column_name, $post_id){
		$option= $this->columns[$column_name];
		$class= $this->class;
		$record= $class::find($post_id);
		if(isset($option["render"]) AND is_callable($option["render"]))
			return $option["render"]($record);
		if($class::is_attr_defined($column_name))
			echo $record->get_meta($column_name);
	}
	function __construct($class, $options=[]){
		$this->class= $class;
		$this->post_type= $class::$post_type;
		if(isset($options["columns"]))
			$this->columns=  $options["columns"];
		if(isset($options["order"]))
			$this->order= $options["order"];
	}
	static function init($custom_post_class){
		$options= [];
		if(isset($custom_post_class::$posts_list_options))
			$options= $custom_post_class::$posts_list_options;
		if(is_callable("{$custom_post_class}::posts_list_options"))
			$options= $custom_post_class::posts_list_options();

		$instance= new static($custom_post_class, $options);
		add_action("load-edit.php", [$instance, "register"]);
	}
}
