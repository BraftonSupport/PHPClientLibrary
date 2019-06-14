<?php 
namespace Brafton\ArticleAPILibrary\Exceptions;

/**
 * Custom Exception XMLNodeException thrown if a required XML element is not found
 * @package SamplePHPApi
 */
class XMLNodeException extends XMLException{
    function __construct($message, $code=""){
        $this->message = "Could not find XMLNode: " . $message;
    }
} 

?>