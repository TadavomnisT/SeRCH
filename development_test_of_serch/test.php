<?php

require_once "Connection_handler.php"; 


$ch = new Connection_handler( "http://7f16bce141c6afad5fe2266b71e280f0.gigfa.com" );

var_dump(
    // openssl_public_encrypt()
    $ch->getBaseUrlContents(
        [
            "post_paramter_1" => hash( "sha256" , rand( 0 , 9999999999 ) ),
            "post_paramter_2" => hash( "sha256" , rand( 0 , 9999999999 ) )
        ]
    )
 );


?>