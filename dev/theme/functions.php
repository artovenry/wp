<?
require dirname(dirname(__DIR__)) . "/vendor/autoload.php";
define("ART_VIEW", __DIR__);
add_action("init",function(){
  Test\Event::register();
});
