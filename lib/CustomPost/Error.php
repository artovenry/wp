<?
namespace Artovenry\Wp\CustomPost;

class Error extends \Exception{}
class TypeIsNotScalar extends Error{}
class RecordNotCustomPost extends Error{}
class RequestNotAuthenticated extends Error{}
class AttributeNotDefined extends Error{
  function __construct($attr){
    $this->message= $attr . " is not defined.";
  }
}