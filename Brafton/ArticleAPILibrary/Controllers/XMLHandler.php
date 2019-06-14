<?php
namespace Brafton\ArticleAPILibrary\Controllers;
require_once 'Connection.php';
/**
 * @package SamplePHPApi
 */
/**
 * class XMLHandler is a helper class to parse the XML feed data
 * @package SamplePHPApi
 */
class XMLHandler {
	/** @var Document */
    private $_doc;

    static $ch;
  
	/**
	 * @param String $url
	 * @return XMLHandler
	 */
	function __construct($url){
        if(strlen($url) == 0){
            return;
        }
        $connect = new Connection($url);
        
        $feed_string = $connect->getFeed();      
        
        try{
            if($connect->response['Content_type'][0] == 'application/xml'){
                if($feed_string){
                    $this->_doc = simplexml_load_string($feed_string);
                }else{
                    throw new XMLLoadException($url);   
                }
            }else{
                throw new XMLInvalidException($url);
            }
        }catch(XMLException $e){
            echo $e->getMessage();
        }
	}
	
    function getNode($element){
        if($this->_doc == null){
            return;
        }
        return $this->_doc->$element;   
    }
    function getChildren($element){ 
        if($this->_doc == null){
            return;
        }
        return $this->_doc->xpath($element);
    }
    function getVal($element){
        if($this->_doc == null){
            return;
        }
        return (string)$this->_doc->$element;   
    }
    function getAttr($element, $attr, $obj = null){ 
        if($obj != null){
            $tmp = $obj->attributes();
            return (string)$tmp[$attr];
        }
        if($this->_doc == null){
            return;
        }
        $tmp = $this->_doc->$element->attributes();
        return (string)$tmp[$attr];
    }
    //FLAGGED FOR DELETION v
	/**
	 * @param String $element
	 * @return String
	 */
	public static function getSetting($element){
		$xh = new XMLHandler("../Classes/settings.xml");
		return $xh->getValue($element);
	}
}


?>