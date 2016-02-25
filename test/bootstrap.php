<?php
require_once '/tmp/wordpress-tests-lib/includes/functions.php';
define("ART_LOGFILE", __DIR__ . "/db.log");

tests_add_filter( 'after_setup_theme', function(){
  require dirname(__DIR__) . "/vendor/autoload.php";
  Test\Event::initialize();
  Test\Info::initialize();
  Test\Blog::initialize();
});

require '/tmp/wordpress-tests-lib/includes/bootstrap.php';
