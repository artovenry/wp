<?
use Test\Event;
class TestEvent extends Artovenry\Wp\CustomPost\Test\UnitTestCase{

  function test_noop(){}

  function setUp(){
    parent::setUp();
    $this->create_post("event", 10);
  }
  function test_sample(){
    $this->assertCount(10, Test\Event::fetch("posts_per_page=10"));
    foreach(Event::where("posts_per_page=3") as $item){
      $this->assertInstanceOf("Test\Event", $item);
    }
    $this->assertEquals(Event::take()->post_status, "publish");
    $this->assertEquals(Event::take()->post_type, "event");
  }
  function test_a_life_without_using_callback(){
    $event= Event::build(get_posts("post_type=event")[0]);
    $event->set_meta("show_at_home", "yes");
    $this->assertEquals($event->show_at_home, "yes");
  }
}

