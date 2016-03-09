<?
require dirname(dirname(__DIR__)) . "/vendor/autoload.php";
Artovenry\Wp\Initializer::run([
	"check_version"=> true,
	"dashboard_column_size"=>2,
	"dashboard_widgets"=>[
		"event"=>"イベント"
	]
]);
