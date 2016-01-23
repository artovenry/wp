<?
class TestSandbox extends Test\UnitTestCase{
  function setUp(){
    parent::setUp();
    $this->events= $this->create_post("event", 10);
  }

  function test_sample(){
    $this->assertCount(10, $this->events);
  }
}


__halt_compiler();
=========================================================================================================
class Media_Test extends WP_UnitTestCase
{
  public function setUp()
  {
    parent::setUp();
    $post_ids = $this->factory->post->create_many( 25 );

    foreach ( $post_ids as $post_id ) {
      $attachment_id = $this->factory->attachment->create_object( 'image-'.$post_id.'.jpg', $post_id, array(
        'post_mime_type' => 'image/jpeg',
        'post_type' => 'attachment'
      ) );
      set_post_thumbnail( $post_id, $attachment_id );
    }
  }
}