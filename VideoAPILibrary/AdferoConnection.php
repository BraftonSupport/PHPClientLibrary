<?php
class AdferoConnection{
    
    public static $instance = null;
    public $url;
    
    public $connection_type = null;
    
    public $doc;
    
    public $response;
    
    private static $force_connection = false;
    
    public function __construct($url){
        $this->url = $url;
        $this->get_connection_type();
        var_dump($this->connection_type);
        
    }
    
    private function get_connection_type(){
        if(self::$force_connection){
            $this->connection_type = self::$force_connection;
            return;
        }
        if(function_exists('fopen') && ini_get('allow_url_fopen')){
            $this->connection_type = 'fopen';
        }
        else if(function_exists('curl_init')){
            $this->connection_type = 'curl';
        }
    }
    public function getFeed(){
        if($this->connection_type != null){
            $connect = $this->connection_type.'_connection';
            if(method_exists($this, $connect)){
                return $this->$connect();
            }
        }
        return false;
    }
    private function curl_connection(){
        $url = $this->url;
        if(!isset($ch)){
          $ch = curl_init();
        }        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-Forwarded-Proto"));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $feed_string = curl_exec($ch); 
        $hinfo = curl_getinfo($ch);
        $this->parseHeaders_curl($hinfo);
        return $feed_string;
    }
    
    private function fopen_connection(){
        $con = file_get_contents($this->url);
        $this->parseHeaders_fopen($http_response_header);
        return $con;
    }
    
    private function parseHeaders_fopen( $headers ){
        $head = array();
        foreach( $headers as $k=>$v )
        {
            $t = explode( ':', $v, 2 );
            if( isset( $t[1] ) )
                $head[ trim($t[0]) ] = trim( $t[1] );
            else
            {
                $head[] = $v;
                if( preg_match( "#HTTP/[0-9\.]+\s+([0-9]+)#",$v, $out ) )
                    $head['http_code'] = intval($out[1]);
            }
        }
        $head['Content_type'] = explode(';',$head['Content-Type']);

        $this->response = $head;
    }
    private function parseHeaders_curl($headers){
        $headers['Content_type'] = explode(';', $headers['content_type']);

        $this->response = $headers;
    }
    public static function force_connection($connection){
        if(self::$force_connection){
            return;   
        }
        self::$force_connection = $connection;
    }
}