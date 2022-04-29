<?php

require_once "Connection_handler.php"; 


$ch = new Connection_handler( "http://7f16bce141c6afad5fe2266b71e280f0.gigfa.com" );


var_dump( 
    $ch -> bypassJsProtection(
        $ch->getBaseUrlContents()
    )
 );



?>