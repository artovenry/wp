<?
namespace Artovenry\Wp;
require_once "errors.php";

class Initializer{
	use Initializer\WpInitializer;
	const DEFAULT_OPTIONS=[
	"disable_update_notification"=> true,
	"check_version"=>true,
	"sweep_wp_header"=>true,
	"disable_auto_emoji_translation"=>false,
	"sweep_dashboard"=>true,
	"dashboard_column_size"=>1,
	];
	private static $initializer;
	private $options;

	static function run($options= []){
		$options= array_merge(self::DEFAULT_OPTIONS, $options);
		if(!isset(self::$initializer))
			self::$initializer= new self($options);
		self::$initializer->initialize_wp();
		self::$initializer->initialize_framework();
	}

	function initialize_framework(){
		require_once "constants.php";
		$this->initialize_models();
		$this->load_controllers();		
		$this->initialize_routes();
		$this->initialize_dashboard();
	}

	//private
		private function initialize_models(){
			foreach(glob(ART_MODEL . "/*.php") as $filename){
				$classname= pathinfo($filename)["filename"];
				require_once $filename;
				$classname::initialize();
			}
		}
		private function initialize_routes(){
			if(!is_readable(ART_ROUTES_YAML))return;
			add_action("after_switch_theme", "\Artovenry\Wp\Route::flush");
			add_action("rest_api_init","\Artovenry\Wp\Route::initialize");
		}
		private function load_controllers(){
			foreach(glob(ART_CONTROLLERS . "/*.php") as $filename){
				$classname= pathinfo($filename)["filename"];
				require_once $filename;
			}
		}
		private function initialize_dashboard(){
	    if(defined("ART_VIEW"))
	      $viewpath= join("/", [ART_VIEW, Dashboard::VIEWPATH]);
	    else
	      $viewpath= Dashboard::VIEWPATH;
			add_action("wp_dashboard_setup", function() use($viewpath){
	      CustomPost\Haml::initialize(Dashboard::VIEWPATH);
				foreach(glob($viewpath . "/*{.php,.haml}", GLOB_BRACE) as $filename){
					$template_name= basename($filename);
					$template_name= explode(".", $template_name)[0];
					$label= isset($this->options["dashboard_widgets"][$template_name])?
						$this->options["dashboard_widgets"][$template_name]: $template_name;
					$widget= new Dashboard($template_name, $label);
					$widget->register();
				}
			});
		}
		private function __construct($options){
			$this->options= $options;
		}
}