<?
use Test\Info;
class TestInfo extends Artovenry\Wp\CustomPost\Test\UnitTestCase{

  function setUp(){
    parent::setUp();
    $this->create_post("info", 5);
  }
  function test_sample(){
    $this->assertEquals(Info::take()->post_type, "info");
  }
}

