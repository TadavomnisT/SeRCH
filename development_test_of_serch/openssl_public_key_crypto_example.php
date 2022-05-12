<?php

// Make a new private key
$private_key = openssl_pkey_new();


// Save Private Key
openssl_pkey_export_to_file($private_key, "privatekey.pem");



// var_dump( 
//     openssl_pkey_get_details($private_key
// );




//Save Public Key
$dn = array(
    // "countryName" => "IN",
    // "stateOrProvinceName" => "Karnataka",
    // "localityName" => "test1",
    // "organizationName" => "test2",
    // "organizationalUnitName" => "test3",
    // "commonName" => "www.test.com",
    // "emailAddress" => "xyz@test.com"
);
$cert = openssl_csr_new($dn, $private_key);
$cert = openssl_csr_sign($cert, null, $private_key, 365);
openssl_x509_export_to_file($cert, "publickey.pem");


// To encrypt data
$data = "This is a secret message!";
$isvalid = openssl_public_encrypt ($data, $crypted , file_get_contents("publickey.pem"),OPENSSL_PKCS1_PADDING);	
echo "Data encryption : ".$crypted;
echo ">br/<>br/<";

if ($isvalid) {	
    openssl_private_decrypt ($crypted, $decrypted , file_get_contents("privatekey.pem"),OPENSSL_PKCS1_PADDING);	
    echo "Data decryption : ".$decrypted;
}


?>