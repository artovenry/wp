<?
class TestPostMeta extends Artovenry\Wp\CustomPost\Test\UnitTestCase{

  function setUp(){
    parent::setUp();
    $this->create_post("info");
    $this->create_post("event");
  }

  function test(){
    $info= Test\Info::take();
    $this->assertTrue(null === $info->show_at_home);
    $info->set_meta("show_at_home", "1");
    $this->assertTrue("1" === $info->show_at_home);
    try{
      $info->set_meta("show_at_homes", "1");
    }catch(Artovenry\Wp\CustomPost\AttributeNotDefined $e){
      $this->assertTrue(true);
    }
    $info->delete_meta("show_at_home");
    $this->assertTrue(null === $info->show_at_home);
 
     try{
      $info->set_meta("show_at_homes", []);
    }catch(Artovenry\Wp\CustomPost\TypeIsNotScalar $e){
      $this->assertTrue(true);
    }
  }

  function test_query(){
    $infos= Test\Info::take(4);
    $first_info = $infos[0];
    $this->assertEquals("info", $first_info->post_type);
    $this->assertEquals("publish", $first_info->post_status);
    $first_info->set_meta("show_at_home", true);

    $this->assertCount(1, Test\Info::all("show_at_home=1"));

    $rs=[];
    foreach(Test\Info::where("show_at_home=1") as $info)
      $rs[]= $info->show_at_home;
    $this->assertCount(1, $rs);
    $this->assertEquals("1", $rs[0]);

    $this->assertEquals("info",Test\Info::take([])->post_type);
  }

  function test_order_query(){
    $events = Test\Event::take(5);
    $events[0]->set_meta([
      "hoge"=>"hoge-1",
      "scheduled_on"=>"2016-01-01"
    ]);
    $events[1]->set_meta([
      "hoge"=>"hoge-2",
      "scheduled_on"=>"2016-01-03"
    ]);
    $events[2]->set_meta([
      "hoge"=>"hoge-3",
      "scheduled_on"=>"2016-01-05"
    ]);
    $events[3]->set_meta([
      "hoge"=>"hoge-4",
      "scheduled_on"=>"2016-01-02"
    ]);
    $events[4]->set_meta([
      "hoge"=>"hoge-5",
      "scheduled_on"=>"2016-01-04"
    ]);
    $events= Test\Event::all([
      "orderby"=>"scheduled_on",
      "order"=>"desc"
    ]);
    $this->assertEquals("hoge-3", $events[0]->hoge);
    $this->assertEquals("hoge-5", $events[1]->hoge);
    $this->assertEquals("hoge-2", $events[2]->hoge);
    $this->assertEquals("hoge-4", $events[3]->hoge);
    $this->assertEquals("hoge-1", $events[4]->hoge);

    $events= Test\Event::all([
      "orderby"=>"scheduled_on",
      "order"=>"asc"
    ]);
    $this->assertEquals("hoge-1", $events[0]->hoge);
    $this->assertEquals("hoge-4", $events[1]->hoge);
    $this->assertEquals("hoge-2", $events[2]->hoge);
    $this->assertEquals("hoge-5", $events[3]->hoge);
    $this->assertEquals("hoge-3", $events[4]->hoge);

  }


  function test_complex_order_query(){
    $events = Test\Event::take(5);
    $events[0]->set_meta([
      "hoge"=>true,
      "scheduled_on"=>"2016-01-01"
    ]);
    $events[1]->set_meta([
      "hoge"=>"hoge-2",
      "scheduled_on"=>"2016-01-03"
    ]);
    $events[2]->set_meta([
      "hoge"=>"hoge-3",
      "scheduled_on"=>"2016-01-05"
    ]);
    $events[3]->set_meta([
      "hoge"=>true,
      "scheduled_on"=>"2016-01-02"
    ]);
    $events[4]->set_meta([
      "hoge"=>true,
      "scheduled_on"=>"2016-01-04"
    ]);
    $events= Test\Event::take(2, [
      "hoge"=>true,
      "orderby"=>"scheduled_on",
      "order"=>"desc"
    ]);
/*    $events= Test\Event::take(2, [
      "orderby"=>"meta_value",
      "order"=>"desc",
      "meta_query"=>[
        //["key"=>"art_event_hoge", "meta_value"=>true],
        ["key"=>"art_event_scheduled_on"],
      ]
    ]);
*/
    $this->assertCount(2, $events);
    $this->assertEquals("2016-01-04", $events[0]->scheduled_on);
    $this->assertEquals("2016-01-02", $events[1]->scheduled_on);
  }
}


__halt_compiler();
-----------------------------------
[
  "show_at_home"=>true,
  "orderby"=>"scheduled_on"
]


[
  "order_by"=>"meta_value"
  "meta_query"=>[
    ""
  ]
]

