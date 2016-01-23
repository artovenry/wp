<?
namespace Test;

class Bootstrap{
  static function run(){
    add_action("init", function(){
      Event::register();
    });
  }
}

