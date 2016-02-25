<?
namespace Artovenry\Wp\CustomPost;
require_once dirname(__DIR__) . "/errors.php";

class Error extends \Artovenry\Wp\Error{}

class TypeIsNotScalar extends Error{}
class RecordNotCustomPost extends Error{}
class RecordNotFound extends Error{}
class RequestNotAuthenticated extends Error{}
class AttributeNotDefined extends Error{
  function __construct($attr){
    $this->message= $attr . " is not defined.";
  }
}
class ForbiddenAttributesError extends Error{}