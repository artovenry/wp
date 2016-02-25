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
		self::$initializer->initialize_framework();
		self::$initializer->initialize_wp();
	}

	function initialize_framework(){
		$this->define_constants();
		$this->initialize_models();
		$this->load_controllers();		
		$this->initialize_routes();
	}

	//private
		private function define_constants(){
			if(!defined("ART_ENV"))
				define("ART_ENV", "development");
			if(!defined("ART_VIEW"))
				define("ART_VIEW", TEMPLATEPATH . "/views");
			if(!defined("ART_MODEL"))
				define("ART_MODEL", TEMPLATEPATH . "/models");
			if(!defined("ART_CONTROLLERS"))
				define("ART_CONTROLLERS", TEMPLATEPATH . "/controllers");
			if(!defined("ART_VERSION_YAML"))
				define("ART_VERSION_YAML", TEMPLATEPATH . "/version.yml");
			if(!defined("ART_ROUTES_YAML"))
				define("ART_ROUTES_YAML", TEMPLATEPATH . "/routes.yml");
			if(!defined("ART_LOGFILE"))
				define("ART_LOGFILE", TEMPLATEPATH . "/db.log");
		}
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
		private function __construct($options){
			$this->options= $options;
		}
}