<?php

//Script by TadavomnisT

// *Note*
// Check out this line:
// 
//            $private_key = openssl_pkey_new();
// 
// Although we named the object as "$private_key" , but,
// It is not just private key!
// In truth, there's a "key-pair" of both public and private key
// generated in that object, but if you pass the object to the functions,
// it'll behave as "private-key" .
// In order to fetch "private-key" as an object , you need to either 
// * (APPROACH#1) Do as what follows ( get detail and fetch)
// or
// * (APPROACH#2)Store in a file in PEM format then fetch ( explained in last part of this code )


// (APPROACH#1) :

$private_key = openssl_pkey_new();
$public_key_pem = openssl_pkey_get_details($private_key)['key'];
$public_key = openssl_pkey_get_public($public_key_pem);

$plain_data = "This is a secret message";

// Test #1
// Encrypt with private-key
// Only dectryptable with public-key------------------------------------------

$cipher_data = "";
$deciphered_data = "";

$state_1 = openssl_private_encrypt($plain_data, $cipher_data, $private_key);
$state_2 = openssl_public_decrypt($cipher_data, $deciphered_data, $public_key);

var_dump(
    $state_1,
    $state_2,
    $cipher_data,
    $deciphered_data
);

// Test #2
// Encrypt with public-key
// Only dectryptable with private-key------------------------------------------

$cipher_data = "";
$deciphered_data = "";

$state_1 = openssl_public_encrypt($plain_data, $cipher_data, $public_key);
$state_2 = openssl_private_decrypt($cipher_data, $deciphered_data, $private_key);

var_dump(
    $state_1,
    $state_2,
    $cipher_data,
    $deciphered_data
);



// ------------------------------------------------------------------------------------

// (APPROACH#2) :

$private_key = openssl_pkey_new();
//Save Private Key
openssl_pkey_export_to_file($private_key, "privatekey.pem");
 
//Save Public Key
$dn = array();
$cert = openssl_csr_new($dn, $private_key);
$cert = openssl_csr_sign($cert, null, $private_key, 365);
openssl_x509_export_to_file($cert, "publickey.pem");
// Or you can store the PEM representation directly.

$public_key = file_get_contents("publickey.pem");

$plain_data = "This is a secret message";

// Test #1
// Encrypt with private-key
// Only dectryptable with public-key------------------------------------------

$cipher_data = "";
$deciphered_data = "";

$state_1 = openssl_private_encrypt($plain_data, $cipher_data, $private_key);
$state_2 = openssl_public_decrypt($cipher_data, $deciphered_data, $public_key);

var_dump(
    $state_1,
    $state_2,
    $cipher_data,
    $deciphered_data
);

// Test #2
// Encrypt with public-key
// Only dectryptable with private-key------------------------------------------

$cipher_data = "";
$deciphered_data = "";

$state_1 = openssl_public_encrypt($plain_data, $cipher_data, $public_key);
$state_2 = openssl_private_decrypt($cipher_data, $deciphered_data, $private_key);

var_dump(
    $state_1,
    $state_2,
    $cipher_data,
    $deciphered_data
);


// ------------------------------------------------------------------------------------


?>