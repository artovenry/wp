<?
use Artovenry\Wp\CustomPost\FormHelper;
class TestHelper extends Artovenry\Wp\CustomPost\Test\UnitTestCase{
	function _setUp(){
		parent::setUp();
		$this->create_post("event", 1);
		$event= Test\Event::take();
		$event->set_meta("show_at_home", "0");
		$event->set_meta("scheduled_on", "sunday");
		$event->set_meta("hoge", "yes");
	}
  function test_noop(){}

	function _test_check_boolean_expected_value(){
		$event= Test\Event::take();
		$this->assertTrue(is_string($event->show_at_home));
		$this->assertEquals($event->show_at_home, "0");

		$event->set_meta("show_at_home", "1");
		$this->assertTrue(is_string($event->show_at_home));
		$this->assertEquals($event->show_at_home, "1");

	}

	function _test_radio_button(){
		extract(FormHelper::helpers());
		$event= Test\Event::take();

		$this->assertEquals($_radio_button($event, "scheduled_on", "sunday"),
			'<input type="radio" name="event[scheduled_on]" id="event_scheduled_on_sunday" value="sunday" checked="checked" />'
		);
		$this->assertEquals($_radio_button($event, "scheduled_on", "monday"),
			'<input type="radio" name="event[scheduled_on]" id="event_scheduled_on_monday" value="monday" />'
		);
		$this->assertEquals($_radio_button($event, "scheduled_on", "sunday", ["id"=>"some-id", "class"=>"some-class"]),
			'<input type="radio" name="event[scheduled_on]" id="some-id" value="sunday" class="some-class" checked="checked" />'
		);
		$this->assertEquals($_radio_button($event, "scheduled_on", "monday", ["checked"=>"checked", "id"=>"some-id", "class"=>"some-class"]),
			'<input type="radio" name="event[scheduled_on]" id="some-id" value="monday" class="some-class" checked="checked" />'
		);
	}

	function _test_check_box(){
		extract(FormHelper::helpers());
		$event= Test\Event::take();

		$this->assertEquals($_check_box($event, "show_at_home"),
			'<input type="hidden" name="event[show_at_home]" value="0" />' .
			'<input type="checkbox" name="event[show_at_home]" id="event_show_at_home" value="1" />'
		);
		$this->assertEquals($_check_box($event, "show_at_home", ["checked"=>"checked"]),
			'<input type="hidden" name="event[show_at_home]" value="0" />' .
			'<input type="checkbox" name="event[show_at_home]" id="event_show_at_home" value="1" checked="checked" />'
		);
		$this->assertEquals($_check_box($event, "hoge", [], "yes", "no"),
			'<input type="hidden" name="event[hoge]" value="no" />' .
			'<input type="checkbox" name="event[hoge]" id="event_hoge" value="yes" checked="checked" />'
		);
		$this->assertEquals($_check_box($event, "hoge", ["id"=>"some-id", "name"=>"some-name"], "yes", "no"),
			'<input type="hidden" name="some-name" value="no" />' .
			'<input type="checkbox" name="some-name" id="some-id" value="yes" checked="checked" />'
		);

	}


}
