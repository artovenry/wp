<?
namespace Artovenry\Wp;
use Symfony\Component\Yaml\Yaml;

abstract class Exception extends \Exception{
  function __construct($message=""){
    parent::__construct($message);
  }
}
class VersionNotSpecified extends Exception{}
class VersionOperatorIsInvalid extends Exception{}
class VersionIsInvalid extends Exception{}
class PluginNotFound extends Exception{}
class PluginNotAllowed  extends Exception{}

class Version{
  //You can only specify  "< > <= >= "as version comparator 
  const VERSION_FORMAT= "/^(>=|<=|>|<)?([0-9.]+)$/";
  static $versions= [];

  static function run(){
    //ONLY CHECK VERSIONS WITH LOGGED-IN
    //add_action("template_redirect", function(){
    //  self::check();
    //});
    add_action("admin_init", function(){
      self::check();
    });


  }

  private static function check(){
    if(!defined("ART_VERSION_YAML") OR !is_readable(ART_VERSION_YAML))
      throw new VersionNotSpecified("Yaml file not found.");
    self::$versions= $versions= Yaml::parse(file_get_contents(ART_VERSION_YAML));
    if(empty($versions["php"]["version"]))
      throw new VersionNotSpecified("PHP version not specified");
    if(empty($versions["wordpress"]["core"]))
      throw new VersionNotSpecified("Wordpress core version not specified");
    if(empty($versions["wordpress"]["plugins"]))
      throw new VersionNotSpecified("Wordpress plugin versions not specified");
    self::check_php_version();
    self::check_wordpress_version();
    self::check_plugins_version();
  }

  private static function check_plugins_version(){
    $active_plugins= [];
    foreach(get_option("active_plugins") as $plugin){
      $key= dirname($plugin);
      $plugins= self::$versions["wordpress"]["plugins"];

      $active_plugins[$key]= $plugin;
      if(!in_array($key, array_keys($plugins)))
        throw new PluginNotAllowed("Plugin $plugin  is not allowed to use.");
    }
 
    foreach(self::$versions["wordpress"]["plugins"] as $plugin_name=>$version){
      $required= is_array($version)? $version["required"]: true;
      if(is_array($version))$version= $version["version"];

      preg_match(self::VERSION_FORMAT, $version,$matches);
      if(empty($matches))throw new VersionOperatorIsInvalid;

      if(!$required)continue;

      $comparator= empty($matches[1]) ? "=" : $matches[1];
      $version= $matches[2];

      if(!in_array($plugin_name, array_keys($active_plugins)))
        throw new PluginNotFound("Plugin $plugin_name is not found.");
      $plugin_version= get_plugin_data(WP_PLUGIN_DIR . "/" . $active_plugins[$plugin_name])["Version"];

      if(!version_compare($plugin_version, $version, $comparator))
        throw new VersionIsInvalid("Plugin $plugin_name requires version $comparator $version");
    }
  }


  private static function check_wordpress_version(){
    preg_match(self::VERSION_FORMAT, self::$versions["wordpress"]["core"],$matches);
    if(empty($matches))throw new VersionOperatorIsInvalid;

    $comparator= empty($matches[1]) ? "=" : $matches[1];
    $version= $matches[2];
    if(!version_compare($GLOBALS["wp_version"], $version, $comparator))
      throw new VersionIsInvalid("Required Wordpress version is $comparator $version");
  }

  private static function check_php_version(){
    preg_match(self::VERSION_FORMAT, self::$versions["php"]["version"],$matches);
    if(empty($matches))throw new VersionOperatorIsInvalid;

    $comparator= empty($matches[1]) ? "=" : $matches[1];
    $version= $matches[2];
    if(!version_compare(PHP_VERSION, $version, $comparator))
      throw new VersionIsInvalid("Required PHP version is $comparator $version");
  }
}