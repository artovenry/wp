#!/usr/bin/env php
<?
require dirname(__DIR__) . "/dev/wp/wp-load.php";
echo wp_create_nonce("wp_rest");