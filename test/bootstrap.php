<?php
require_once '/tmp/wordpress-tests-lib/includes/functions.php';

tests_add_filter( 'after_setup_theme', function(){
  require dirname(__DIR__) . "/vendor/autoload.php";
  Test\Event::register();
  Test\Info::register();
});

require '/tmp/wordpress-tests-lib/includes/bootstrap.php';
