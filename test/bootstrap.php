<?php
require dirname(__DIR__) . "/vendor/autoload.php";
require_once '/tmp/wordpress-tests-lib/includes/functions.php';

tests_add_filter( 'muplugins_loaded', function(){
  Test\Bootstrap::run();  
});

require '/tmp/wordpress-tests-lib/includes/bootstrap.php';
