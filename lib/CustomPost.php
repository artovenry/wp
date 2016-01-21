<?
namespace Artovenry\Wp\CustomPost;
/*
カスタムポストの基底クラス
サブクラスで、カスタムポストタイプの実装を行う
ビジネスロジックはカスタムポストタイプの拡張属性について行う。
ビジネスロジックはWPのsave_postアクションフック、wp_insert_postのコールバックとして実装する。フレームワークではバリデーションを実装しない。

拡張属性：
	WPのwp_postsテーブルのカラムにマップされない属性のこと
	wp_postmetaテーブルに保存される
	型は文字列型のみ

	post_type:  art_info
	meta_key:   art_info_show_at_home


 */
abstract class Base{
}


__halt_compiler();

class Info extends Artovenry\Wp\CustomPost\Base{
	static $post_type= "info";
	static $meta_attributes=["show_at_home", "show_title"];
	static $meta_box_options=[						//optional
		"show_at_home"=>[										//key must be polulated at $meta_attributes
			"label"=>"トップページに表示",		//required
			"context"=>"advanced",						// default: "side"
			"priority"=>"low",								//default: "core"
			//"template"=>										//default is the name of the attribute
		]
	];

	//Or, return hash of meta_box_options
	static function meta_box_options= function(){
		return [
			(...)
		]
	}



}

//retrieve posts which post_type is "art_info" order by desc limit n("n" comes from WP's option settings)
//fetchはジェネレーターとして実装する（Infoクラスに変換する）
$informations= Info::fetch(); //simply proxies to get_posts()
foreach($informations as $item){
	echo $item->show_at_home();  //Display meta_attribute "show_at_home"
	echo $item->art_show_at_home();  //Also Display meta_attribute "show_at_home"
	echo $item->post_date; //attributes on WP_Post are transparently accessible
	$item->post; //derives native WP_Post object
}

Info::build($post_or_post_id) //returns an instance of Info class, assosiated with $post, if the post_type doesn't match with "info" 

Info::show_at_home($post_or_post_id)// returns value of "show_at_home" of the $post, if the post_type doesn't match with "info", throws Artovenry\Wp\CustomPost\PostTypeMismatch error.



//ルーティングはWP標準に従う

index.php?post_type=art_info
=>/info

index.php?post_type=art_info&p=123
=>/info/123

index.php?post_type=art_info&year=2013&month=5&day=23
=>/info/archives/2013/5/23

