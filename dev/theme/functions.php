<?
require dirname(dirname(__DIR__)) . "/vendor/autoload.php";
add_action("init",function(){
  Test\Event::register();
});
