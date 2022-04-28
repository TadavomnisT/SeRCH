<?php

// toHex(slowAES.decrypt(c,2,a,b))
// decrypt:function(cipherIn,mode,key,iv)

// var a=toNumbers("f655ba9d09a112d4968c63579db590b4"),b=toNumbers("98344c2eee86c3994890592585b49f80"),c=toNumbers("15e749cc6e2a26b61a6e480e9ee038cf");
               


// modeOfOperation:{
//     OFB:0,
//     CFB:1,
//     CBC:2
// },


$a = "f655ba9d09a112d4968c63579db590b4";
$b = "98344c2eee86c3994890592585b49f80";
$c = "15e749cc6e2a26b61a6e480e9ee038cf";

$data = pack("H*" , $c);
$key = pack("H*" , $a);
$iv = pack("H*" , $b);

require_once "SlowAES.php";

$aes = new AES;



var_dump(
    // bin2hex($data),
    // bin2hex($key),
    // bin2hex($iv),
    // str_split( $key , 1 ),
    $aes->decrypt(toNumbers($c), 16, 2, toNumbers($a), 16, toNumbers($b)),
    toHex($aes->decrypt(toNumbers($c), 16, 2, toNumbers($a), 16, toNumbers($b))),
    // $aes -> decrypt( $data ,16,2, str_split( $key , 1 ),128,$iv)
);
die;

$avalable = [];
foreach (openssl_get_cipher_methods() as $value) {
    if (    
        strpos( strtolower($value) , "aes" ) !== false 
        // && strpos( strtolower($value) , "cbc" ) !== false
    ) {
        $avalable[] = $value ;
    }
}

foreach ($avalable as $algo) {
    var_dump(
        $algo,
        openssl_decrypt( $data , $algo , $key , OPENSSL_RAW_DATA , $iv  ),
        bin2hex( openssl_decrypt( $data , $algo , $key , OPENSSL_RAW_DATA , $iv  ) ),
        // openssl_error_string ()    
    );
}

foreach ($avalable as $algo) {
    var_dump(
        $algo,
        openssl_decrypt( $data , $algo , $key , OPENSSL_ZERO_PADDING , $iv  ),
        bin2hex( openssl_decrypt( $data , $algo , $key , OPENSSL_ZERO_PADDING , $iv  ) ),
        // openssl_error_string ()    
    );
}

foreach ($avalable as $algo) {
    var_dump(
        $algo,
        openssl_decrypt( $data , $algo , $key , 0 , $iv  ),
        bin2hex( openssl_decrypt( $data , $algo , $key , 0 , $iv  ) ),
        // openssl_error_string ()    
    );
}


die;
var_dump(
    $avalable,
    $data,
    $key,
    $iv,
    openssl_get_cipher_methods()[0],
    openssl_decrypt( $data , openssl_get_cipher_methods()[0] , $key , OPENSSL_RAW_DATA , $iv  ),
    openssl_error_string ()    
);



// func==========================

function toHex($args){
    if(func_num_args() != 1 || !is_array($args)){
        $args = func_get_args();
    }
    $ret = '';
    for($i = 0; $i < count($args) ;$i++)
        $ret .= sprintf('%02x', $args[$i]);
    return $ret;
}

function toNumbers($s){
    $ret = array();
    for($i=0; $i<strlen($s); $i+=2){
        $ret[] = hexdec(substr($s, $i, 2));
    }
    return $ret;
}

function getRandom($min,$max){
    if($min === null)
        $min = 0;
    if($max === null)
        $max = 1;
    return mt_rand($min, $max);
}

function generateSharedKey($len){
    if($len === null)
        $len = 16;
    $key = array();
    for($i = 0; $i < $len; $i++)
        $key[] = getRandom(0,255);
    return $key;
}

function generatePrivateKey($s,$size){
    if(function_exists('mhash') && defined('MHASH_SHA256')){
        return convertStringToByteArray(substr(mhash(MHASH_SHA256, $s), 0, $size));
    }else{
        throw new Exception('cryptoHelpers::generatePrivateKey currently requires mhash');
    }
}

function convertStringToByteArray($s){
    $byteArray = array();
    for($i = 0; $i < strlen($s); $i++){
        $byteArray[] = ord($s[$i]);
    }
    return $byteArray;
}

function convertByteArrayToString($byteArray){
    $s = '';
    for($i = 0; $i < count($byteArray); $i++){
        $s .= chr($byteArray[$i]);
    }
    return $s;
}


?>