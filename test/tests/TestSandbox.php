<?
class TestSandbox extends WP_UnitTestCase{
  function test_sample(){
    new Test\Event;
    $this->assertTrue( true );
  }
}