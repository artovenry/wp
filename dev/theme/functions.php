<?
require dirname(dirname(__DIR__)) . "/vendor/autoload.php";
define("ART_ENV", "development");
define("ART_VIEW", __DIR__);
Test\Event::initialize();
Test\Info::initialize();
Test\Blog::initialize();

/*Artovenry\Wp\Dashboard::init([
	"update_notification"=> false,
	"menu"=>[


	]
]);*/