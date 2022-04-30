<?php

require_once "Connection_handler.php"; 


$ch = new Connection_handler( "http://7f16bce141c6afad5fe2266b71e280f0.gigfa.com" );


var_dump( 
    $ch->getBaseUrlContents(
        [
            "post_test" => "this is post test",
            "post_test1" => "this is post test"
        ]
    )
 );



?>