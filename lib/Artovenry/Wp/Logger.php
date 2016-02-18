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
    //echo "{$sql}\n";
    //error_log("\n{$sql}\n");
    error_log("\n{$sql}\n", 3, TEMPLATEPATH . "/tmp/db.log");
    return $sql;
  }
}
