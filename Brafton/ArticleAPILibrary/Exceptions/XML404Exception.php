<?php 
namespace Brafton\ArticleAPILibrary\Exceptions;

class XML404Exception extends XMLException{
    function __construct($message, $code = 2){
        $this->message = "A 404 Page not found error was returned for ".$message; 
    }
}

?>