<?php

/*
Proxy format: [ "proxy_type" => "host:port" ]
    Examle:
    $proxy = [ "http" => "127.0.0.1:8585" ];
    $proxy = [ "socks" => "127.0.0.1:9050" ];
*/


require_once "SlowAES.php";

class Connection_handler {
    
    private $base_url , $proxy , $cookie;

    public function __construct( string $base_url = NULL , $proxy = FALSE ) {
        $this->base_url = $base_url;
        $this->proxy = $proxy;
        $this->aes = new AES;
    }

    public function setUrl( string $base_url ) {
        $this->base_url = $base_url;
    }

    public function getUrl( ) {
        return $this->base_url;
    }

    public function setProxy( $proxy = FALSE ) {
        $this->proxy = $proxy;
    }

    public function getProxy( ) {
        return $this->proxy;
    }

    public function setCookie( $cookie = FALSE ) {
        $this->cookie = $cookie;
    }

    public function getCookie( ) {
        return $this->cookie;
    }

    public function getBaseUrlContents( ) {
        return $this->getUrlContents( $this->base_url );
    }

    public function getUrlContents( string $url ) {
        $ch = curl_init (); 
        curl_setopt ($ch, CURLOPT_URL, $url); 
        if ( isset( $this->proxy["socks"] ) || isset( $this->proxy["http"] ) )
            curl_setopt ($ch, CURLOPT_PROXY, ( isset( $this->proxy["http"] ) ) ? $this->proxy["http"] : $this->proxy["socks"] ); 
        if ( isset( $this->proxy["socks"] ) )
            curl_setopt ($ch, CURLOPT_PROXYTYPE, 7);        
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, TRUE); 
        curl_setopt ($ch, CURLOPT_FAILONERROR, true); 
        curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1); 
        $data = curl_exec($ch); 
        curl_close ($ch);
        return $data;
    }

    public function bypassJsProtection( string $html ) {
        $a = $this->getInbetweenStrings( "var a=toNumbers(\"" , "\")" , $html );
        $b = $this->getInbetweenStrings( ",b=toNumbers(\"" , "\")" , $html );
        $c = $this->getInbetweenStrings( ",c=toNumbers(\"" , "\")" , $html );
        $cookie_name = $this->getInbetweenStrings( "document.cookie=\"" , "=\"+toHex" , $html );
        $hash = $this->toHex(
            $this->aes->decrypt(
                $this->toNumbers($c), 16, 2, $this->toNumbers($a), 16, $this->toNumbers($b)
            )
        );
        $link = $this->getInbetweenStrings( "location.href=\"" , "\";" , $html );
        var_dump(
            $cookie_name,
            $link,
            $hash
        );
    }

    public function getInbetweenStrings ( $start, $end , $string)
    {
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }

    public function toHex($args) {
        if(func_num_args() != 1 || !is_array($args)){
            $args = func_get_args();
        }
        $ret = '';
        for($i = 0; $i < count($args) ;$i++)
            $ret .= sprintf('%02x', $args[$i]);
        return $ret;
    }
    
    public function toNumbers($s) {
        $ret = array();
        for($i=0; $i<strlen($s); $i+=2){
            $ret[] = hexdec(substr($s, $i, 2));
        }
        return $ret;
    }
    
    public function getRandom($min,$max) {
        if($min === null)
            $min = 0;
        if($max === null)
            $max = 1;
        return mt_rand($min, $max);
    }
    
    public function generateSharedKey($len) {
        if($len === null)
            $len = 16;
        $key = array();
        for($i = 0; $i < $len; $i++)
            $key[] = $this->getRandom(0,255);
        return $key;
    }
    
    public function generatePrivateKey($s,$size) {
        if(function_exists('mhash') && defined('MHASH_SHA256')){
            return $this->convertStringToByteArray(substr(mhash(MHASH_SHA256, $s), 0, $size));
        }else{
            throw new Exception('cryptoHelpers::generatePrivateKey currently requires mhash');
        }
    }
    
    public function convertStringToByteArray($s) {
        $byteArray = array();
        for($i = 0; $i < strlen($s); $i++){
            $byteArray[] = ord($s[$i]);
        }
        return $byteArray;
    }
    
    public function convertByteArrayToString($byteArray) {
        $s = '';
        for($i = 0; $i < count($byteArray); $i++){
            $s .= chr($byteArray[$i]);
        }
        return $s;
    }

}


?>
