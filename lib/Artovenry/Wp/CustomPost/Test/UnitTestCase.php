<?
namespace Artovenry\Wp\CustomPost\Test;
use Artovenry\Wp\CustomPost\Constants;

class UnitTestCase extends \Wp_UnitTestCase{
  const DEFAULT_NUM_FOR_CREATE_POST= 10;

  protected static function factory_for($post_type) {
/*    static $factory = null;
    if ( ! $factory ) {
      $factory = new UnitTest_Factory($post_type);
    }
    return $factory;
*/
    return new UnitTest_Factory($post_type);
  }

  //SHOULD RESIDES HERE
  function __get( $name ) {
    if(preg_match("/\Afactory_(\w+)\z/",$name, $matches))
      return self::factory_for($matches[1]);
  }

  function create_post($post_type, $num=self::DEFAULT_NUM_FOR_CREATE_POST){
    $factory= "factory_{$post_type}";
    return $this->$factory->custom_post->create_many($num);
  }
}

class UnitTest_Factory extends \WP_UnitTest_Factory{
  function __construct($post_type){
    parent::__construct();
    $this->custom_post= new WP_UnitTest_Factory_For_CustomPost($post_type, $this);
  }
}

class WP_UnitTest_Factory_For_CustomPost extends \WP_UnitTest_Factory_For_Post{
  function __construct($post_type, $factory= null){
    parent::__construct( $factory );
    $this->default_generation_definitions['post_type']= $post_type;
  }
}