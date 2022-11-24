<?php

//Script by TadavomnisT


$c = new SeRCH;
$c->print_intro_message();

$menu = [
    [
        "Command" => "/getpkey",
        "Description" => "Prints the generated public key",
    ],
    [
        "Command" => "/private_enc {BASE64_DATA}",
        "Description" => "Encrypts base64-encoded data with private key and returns cipher in base64 encoding",
    ],
    [
        "Command" => "/public_enc  {BASE64_DATA}",
        "Description" => "Encrypts base64-encoded data with public key and returns cipher in base64 encoding",
    ],
    [
        "Command" => "/private_dec {BASE64_CIPHER}",
        "Description" => "Decrypts base64-encoded cipher data with private key",
    ],
    [
        "Command" => "/public_dec  {BASE64_CIPHER}",
        "Description" => "Decrypts base64-encoded cipher data with public key",
    ]
];
$m = new ArrayToTextTable( $menu );
// $m->maxLineLength( 30 );
echo $m->render() . PHP_EOL;


if( !file_exists( "session.json" ) )
{
    echo "Hello Hello:3" .PHP_EOL;
    echo "Looks like it's your first time using Serch!" .PHP_EOL;
    echo "For your own safety, You MUST set a password for your session." .PHP_EOL;

}



echo "Send a command: ";
$command = fread( STDIN , 4096 );
var_dump( $command );



class SeRCH
{
    public function print_intro_message()
    {
      echo "                \033[33m.;_               :lo;\033[0m                   .:\033[36m Secure Relay Chat on HTTP\033[0m:.\n                \033[33m#:lx:#,ldkOkOkkxdool0K:\033[0m\n                \033[33m'KxldkKXXXXXXXXXXXXOol\033[0m      \033[91mAUTHOR :         \033[0m TadavomnisT (Behrad.B)\n                 \033[33m,o0XXXXXXXXXXXXXXXXXd\033[0m      \033[91mRepo :           \033[0m https://github.com/TadavomnisT/SeRCH\n                 \033[33mdKXXXXXXXXXXXXXXKXXXK#\033[0m     \033[91mREPORTING BUGS : \033[0m https://github.com/TadavomnisT/SeRCH/issues\n                \033[33m'0XXX0_KXXXXXXXX_kXXXK#\033[0m     \033[91mCOPYRIGHT :\033[0m\n                 \033[33m0XXXXKXX0d::cdKXXXXKc#\033[0m        Copyright © 2022   License GPLv3+\n                 \033[33m#0XXXXXKl.x,o.lXXXK##\033[0m         This is free software: you are free to change and redistribute it.\n                  \033[33m0XXXXkK0.0.0KXXXXXX\033[0m          There is NO WARRANTY, to the extent permitted by law.\n                 \033[96m':lo\033[0m\033[33mdxkkdXXXXXXXX\033[0m\033[0m\033[96mkkkkk:\033[0m \n     \033[95m###########\033[0m\033[96m  ;kkkkxdllkkkxclcxkkk:\033[0m        \033[95m#############              ##########        ###             ###     \n   \033[95m####      ####\033[0m\033[96m 'kkkkxxdoxxxddxkkkkkdl\033[0m       \033[95m##          ###          ####      ####      ###             ###     \n \033[95m###            ###\033[0m\033[96mdkkkkkkxc  :ddkkkkkkxc\033[0m      \033[95m##     #      ###       ##            ##     ###             ###     \n \033[95m##       . #   ###\033[0m\033[96mokkkkkxd;\033[0m\033[95m###\033[0m\033[96modddddddd,\033[0m     \033[95m###             ###     ## .         . ###    ###        #    #####   \n \033[95m###   .         #\033[0m\033[96m,xkkxxddo#    ;kkkkkxo#\033[0m      \033[95m##   .     #    ##     ##   .     .    #     ####            ###     \n  \033[95m###.            #\033[0m\033[96m'dxdxkd'#     #d# ,'\033[0m\033[95m###     ##             ###     ##     . .            ###################     \n    \033[95m############       \033[96m;l'\033[0m         \033[96m#\033[0m   \033[0m\033[95m###     ##     #      ###      ##     . .            ####       .  #### #   \n               \033[95m###      ##             ###     ##           ###       ##   .     .          ###             ###     \n                ###     ##################     #############          ## .         .        ###     .       ###     \n ##             ###     ####     .       .     ####       ##          ##             ###    ##### .      .  ###     \n ###           ####.    ####   .   .   .    .  ####  .     ###        ####          ###     ##### .         ###     \n  ###############   .  .  ####       .   #   . ##      .    ### #       ####      ###       ###     .       ###     \n      #######        .       #############    .##        .    ###          #######        #####      .      ###                                                                                                                                     \033[0m\n"; 
    }
}






// namespace dekor;

// use function array_keys;

/**
 * @author Denis Koronets
 */
class ArrayToTextTable
{
    /**
     * @var array
     */
    private $data;
    
    /**
     * @var array
     */
    private $columnsList = [];
    
    /**
     * @var int
     */
    private $maxLineLength = 40;
    
    /**
     * @var array
     */
    private $columnsLength = [];
    
    /**
     * @var array
     */
    private $result = [];
    
    /**
     * @var string
     */
    private $charset = 'UTF-8';
    
    /**
     * @var bool
     */
    private $renderHeader = true;
    
    public function __construct(array $data)
    {
        $this->data = $data;
    }
    
    /**
     * Set custom charset for columns values
     *
     * @param $charset
     *
     * @return \dekor\ArrayToTextTable
     * @throws \Exception
     */
    public function charset($charset)
    {
        if (!in_array($charset, mb_list_encodings())) {
            throw new \Exception(
                'This charset `' . $charset . '` is not supported by mbstring.' .
                'Please check it: http://php.net/manual/ru/function.mb-list-encodings.php'
            );
        }
        
        $this->charset = $charset;
        
        return $this;
    }
    
    /**
     * Set mode to print no header in the table
     *
     * @return self
     */
    public function noHeader()
    {
        $this->renderHeader = false;
        
        return $this;
    }
    
    /**
     * @param int $length
     *
     * @return self
     * @throws \Exception
     */
    public function maxLineLength($length)
    {
        if ($length < 3) {
            throw new \Exception('Minimum length for cropper is 3 sumbols');
        }
        
        $this->maxLineLength = $length;
        
        return $this;
    }
    
    /**
     * Build your ascii table and return the result
     *
     * @return string
     */
    public function render()
    {
        if (empty($this->data)) {
            return 'Empty';
        }
        
        $this->calcColumnsList();
        $this->calcColumnsLength();
        
        /** render section **/
        $this->renderHeader();
        $this->renderBody();
        $this->lineSeparator();
        /** end render section **/
        
        return str_replace(
            ['++', '||'],
            ['+', '|'],
            implode(PHP_EOL, $this->result)
        );
    }
    
    /**
     * Calculates list of columns in data
     */
    protected function calcColumnsList()
    {
        
        $this->columnsList = array_keys(reset($this->data));
    }
    
    /**
     * Calculates length for string
     *
     * @param $str
     *
     * @return int
     */
    protected function length($str)
    {
        return mb_strlen($str, $this->charset);
    }
    
    /**
     * Calculate maximum string length for each column
     */
    private function calcColumnsLength()
    {
        foreach ($this->data as $row) {
            if ($row === '---') {
                continue;
            }
            foreach ($this->columnsList as $column) {
                $this->columnsLength[$column] = max(
                    isset($this->columnsLength[$column])
                        ? $this->columnsLength[$column]
                        : 0,
                    $this->length($row[$column]),
                    $this->length($column)
                );
            }
        }
    }
    
    /**
     * Append a line separator to result
     */
    private function lineSeparator()
    {
        $tmp = '';
        
        foreach ($this->columnsList as $column) {
            $tmp .= '+' . str_repeat('-', $this->columnsLength[$column] + 2) . '+';
        }
        
        $this->result[] = $tmp;
    }
    
    /**
     * @param $columnKey
     * @param $value
     *
     * @return string
     */
    private function column($columnKey, $value)
    {
        return '| ' . $value . ' ' . str_repeat(' ', $this->columnsLength[$columnKey] - $this->length($value)) . '|';
    }
    
    /**
     * Render header part
     *
     * @return void
     */
    private function renderHeader()
    {
        $this->lineSeparator();
        
        if (!$this->renderHeader) {
            return;
        }
        
        $tmp = '';
        
        foreach ($this->columnsList as $column) {
            $tmp .= $this->column($column, $column);
        }
        
        $this->result[] = $tmp;
        
        $this->lineSeparator();
    }
    
    /**
     * Render body section of table
     *
     * @return void
     */
    private function renderBody()
    {
        foreach ($this->data as $row) {
            if ($row === '---') {
                $this->lineSeparator();
                continue;
            }
            
            $tmp = '';
            
            foreach ($this->columnsList as $column) {
                $tmp .= $this->column($column, $row[$column]);
            }
            
            $this->result[] = $tmp;
        }
    }
}




die;






















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