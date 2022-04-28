<?php

require_once "SlowAES.php";


/*
Proxy format: [ "proxy_type" => "host:port" ]
    Examle:
    $proxy = [ "http" => "127.0.0.1:8585" ];
    $proxy = [ "socks" => "127.0.0.1:9050" ];
*/

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
        # code...
    }


}


?>
