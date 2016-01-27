<?
global $wp_query;
var_dump($wp_query->query);
while(have_posts()){
	the_post();
	var_dump($post);
	echo "<hr />";
}