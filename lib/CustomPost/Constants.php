<?
namespace Artovenry\Wp\CustomPost;

abstract class Constants{
  const PREFIX= "art";
  const DEFAULT_POST_TYPE_OPTIONS=[
    "public"=>true,
    "supports"=>["title", "editor", "author", "thumbnail",  "excerpt",  "revisions"],
    "hierarchical"=>false,
    "rewrite"=>false
  ];
  const DEFAULT_META_BOX_PRIORITY= "core";
  const DEFAULT_META_BOX_CONTEXT="side";
}
