<?
namespace Artovenry\Wp;

class Logger{
  static function start(){
    add_filter("query", __CLASS__ . "::log");
  }

  static function finish(){
    remove_filter("query",  __CLASS__ . "::log");
  }

  static function log($sql){
    echo "\n\n{$sql}\n\n";
    return $sql;
  }
}
