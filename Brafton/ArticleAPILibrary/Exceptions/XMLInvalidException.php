<?php 
namespace Brafton\ArticleAPILibrary\Exceptions;

class XMLInvalidException extends XMLException{
    function __construct($message, $code = 2){
        $this->message = "No Valid XML was returned from ".$message; 
    }
}