<?
namespace Artovenry\Wp;

abstract class AbstractMetaBox{
  const PREFIX= ART_PREFIX;
  protected $prefixed_name;
  protected $options;

	abstract function render($post, $args);
	abstract function register();
  function nonce_key(){
    return $this->prefixed_name;
  }
  function nonce_name(){
    return CsrfAuthorization::token_for($this->options["name"]);
  }
  function __construct($name, $options){
  	$this->prefixed_name= join("_", [self::PREFIX, $name]); 
    if(!isset($options["label"]))
      $options["label"]= $options["name"];
    if(isset($options["args"]))
      $options["args"]= (array)$options["args"];
  	$this->options= $options;
  }
}
