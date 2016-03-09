<?
namespace Artovenry\Wp;
class Dashboard extends AbstractMetaBox{
  const VIEWPATH= "dashboard_widgets";

  function register(){
    extract($this->options);
    wp_add_dashboard_widget($this->prefixed_name, $label, [$this, "render"]);
  }
  function render($post, $args){
    extract($this->options);
    CustomPost\Haml::render_box($template, [], $this->nonce_key(),$this->nonce_name());
  }

  function __construct($template_name, $label){
    $options= [
      "template"=>$template_name,
      "label"=>$label,
    ];
    parent::__construct($template_name, $options);
  }
}