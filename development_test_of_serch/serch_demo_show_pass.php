<?php

//Script by TadavomnisT


$c = new SeRCH;
$c->print_intro_message();
$c->print_help();
$c->check_session();
$c->get_command();




class SeRCH
{
    private $public_key , $private_key;
    public function print_intro_message()
    {
      echo "                \033[33m.;_               :lo;\033[0m                   .:\033[36m Secure Relay Chat on HTTP\033[0m:.\n                \033[33m#:lx:#,ldkOkOkkxdool0K:\033[0m\n                \033[33m'KxldkKXXXXXXXXXXXXOol\033[0m      \033[91mAUTHOR :         \033[0m TadavomnisT (Behrad.B)\n                 \033[33m,o0XXXXXXXXXXXXXXXXXd\033[0m      \033[91mRepo :           \033[0m https://github.com/TadavomnisT/SeRCH\n                 \033[33mdKXXXXXXXXXXXXXXKXXXK#\033[0m     \033[91mREPORTING BUGS : \033[0m https://github.com/TadavomnisT/SeRCH/issues\n                \033[33m'0XXX0_KXXXXXXXX_kXXXK#\033[0m     \033[91mCOPYRIGHT :\033[0m\n                 \033[33m0XXXXKXX0d::cdKXXXXKc#\033[0m        Copyright © 2022   License GPLv3+\n                 \033[33m#0XXXXXKl.x,o.lXXXK##\033[0m         This is free software: you are free to change and redistribute it.\n                  \033[33m0XXXXkK0.0.0KXXXXXX\033[0m          There is NO WARRANTY, to the extent permitted by law.\n                 \033[96m':lo\033[0m\033[33mdxkkdXXXXXXXX\033[0m\033[0m\033[96mkkkkk:\033[0m \n     \033[95m###########\033[0m\033[96m  ;kkkkxdllkkkxclcxkkk:\033[0m        \033[95m#############              ##########        ###             ###     \n   \033[95m####      ####\033[0m\033[96m 'kkkkxxdoxxxddxkkkkkdl\033[0m       \033[95m##          ###          ####      ####      ###             ###     \n \033[95m###            ###\033[0m\033[96mdkkkkkkxc  :ddkkkkkkxc\033[0m      \033[95m##     #      ###       ##            ##     ###             ###     \n \033[95m##       . #   ###\033[0m\033[96mokkkkkxd;\033[0m\033[95m###\033[0m\033[96modddddddd,\033[0m     \033[95m###             ###     ## .         . ###    ###        #    #####   \n \033[95m###   .         #\033[0m\033[96m,xkkxxddo#    ;kkkkkxo#\033[0m      \033[95m##   .     #    ##     ##   .     .    #     ####            ###     \n  \033[95m###.            #\033[0m\033[96m'dxdxkd'#     #d# ,'\033[0m\033[95m###     ##             ###     ##     . .            ###################     \n    \033[95m############       \033[96m;l'\033[0m         \033[96m#\033[0m   \033[0m\033[95m###     ##     #      ###      ##     . .            ####       .  #### #   \n               \033[95m###      ##             ###     ##           ###       ##   .     .          ###             ###     \n                ###     ##################     #############          ## .         .        ###     .       ###     \n ##             ###     ####     .       .     ####       ##          ##             ###    ##### .      .  ###     \n ###           ####.    ####   .   .   .    .  ####  .     ###        ####          ###     ##### .         ###     \n  ###############   .  .  ####       .   #   . ##      .    ### #       ####      ###       ###     .       ###     \n      #######        .       #############    .##        .    ###          #######        #####      .      ###                                                                                                                                     \033[0m\n"; 
    }
    public function check_session()
    {
        if( !file_exists( "session.srch" ) )
        {
            echo "Hello Hello:3" .PHP_EOL;
            echo "Looks like it's your first time using SeRCH-Client!" .PHP_EOL;
            echo "For your own safety, You MUST set a password for your session." .PHP_EOL . PHP_EOL;
            while (true) {
                $password = $this->prompt_silent("Enter password (will not be echoed):");
                if ( $password === $this->prompt_silent("Verify password (will not be echoed):") )
                    break;
                else echo PHP_EOL . "[*]ERROR: Passwords doesn't match! Try again." . PHP_EOL . PHP_EOL;
            }
            $this->private_key = openssl_pkey_new();
            openssl_pkey_export( $this->private_key , $private_key_pem);
            $this->public_key = openssl_pkey_get_details($this->private_key)['key'];
            $data = json_encode(["private" => $private_key_pem, "public" => $this->public_key]);
            $cipher = "aes-256-cbc"; 
            $encryption_key = $password; 
            $iv = "0000000000000000";  
            $encrypted_data = openssl_encrypt($data, $cipher, $encryption_key, 0, $iv); 
            file_put_contents( "session.srch" , $encrypted_data ); 
            echo "[+] Session created successfully." . PHP_EOL . PHP_EOL;
        }
        else
        {
            while (true) {
                $password = $this->prompt_silent("Enter password please (will not be echoed):");
                $cipher = "aes-256-cbc"; 
                $encryption_key = $password; 
                $iv = "0000000000000000";  
                $encrypted_data = file_get_contents( "session.srch" ); 
                $data = openssl_decrypt($encrypted_data, $cipher, $encryption_key, 0, $iv);
                if( $data ) break;
                else echo "[*]ERROR: Wronge password?" . PHP_EOL . PHP_EOL;
            }
            $json = json_decode($data , true);
            if( isset( $json["public"] ) && isset( $json["private"] ) )
            {
                $this->private_key = openssl_pkey_get_private( $json["private"] );
                if ( !$this->private_key ) die("[*]ERROR: Corrupted data in session?");
                $this->public_key = $json["public"];
                echo "[+] Session decrypted successfully." . PHP_EOL . PHP_EOL;
            }
            else die("[*]ERROR: Corrupted data in session?");
        }
    }
    public function get_command()
    {
        while (true) {
            echo "Send a command: ";
            $command = explode( " " , trim(fread( STDIN , 4096 )));
            if( $command[0] === "/getpkey" )
            {
                echo "Copy below text:" . PHP_EOL . PHP_EOL;
                echo $this->public_key . PHP_EOL . PHP_EOL;
            }
            else if( $command[0] === "/help" )
            {
                $this->print_help();
            }
            else if( $command[0] === "/exit" )
            {
                break;
            }
            else if( $command[0] === "/file_private_dec" )
            {
                if ( file_exists($command[1]) ) {
                    if ( !file_exists($command[2]) ) {
                        $input = fopen($command[1],'r');
                        $output = fopen($command[2],'w');
                        while (true) {
                            $buffer = fread($input, 256);
                            if (strlen($buffer) == 0)
                                break;
                            $result = openssl_private_decrypt($buffer, $deciphered_data, $this->private_key , OPENSSL_PKCS1_OAEP_PADDING);
                            fwrite( $output , $deciphered_data );
                        }
                        fclose( $input );
                        fclose( $output );
                        echo "[+] Successfully decrypted in " . $command[2] . PHP_EOL . PHP_EOL;
                    } else echo "[*]ERROR: Output file Already exists." . PHP_EOL . PHP_EOL;
                } else echo "[*]ERROR: Input file Not found." . PHP_EOL . PHP_EOL;
            }
            else if( $command[0] === "/file_enc_new_pkey" )
            {
                if ( file_exists($command[1]) ) {
                    if ( !file_exists($command[2]) ) {
                        echo PHP_EOL . "Please enter a new public-key:" . PHP_EOL . PHP_EOL;
                        $data = "";
                        while (true) {
                            $line = fread( STDIN , 4096 );
                            $data .= $line;
                            if (trim($line) == "-----END PUBLIC KEY-----")
                                break;
                        }
                        $new_public_key = trim($data) . PHP_EOL;
                        $input = fopen($command[1],'r');
                        $output = fopen($command[2],'w');
                        while (!feof($input)) {
                            $buffer = fgets($input, 64);
                            openssl_public_encrypt($buffer, $ciphered_data, $new_public_key , OPENSSL_PKCS1_OAEP_PADDING );
                            fwrite( $output , $ciphered_data );
                        }
                        fclose( $input );
                        fclose( $output );
                        echo "[+] Successfully encrypted in " . $command[2] . PHP_EOL . PHP_EOL;
                    } else echo "[*]ERROR: Output file Already exists." . PHP_EOL . PHP_EOL;
                } else echo "[*]ERROR: Input file Not found." . PHP_EOL . PHP_EOL;
            }
        }
    }
    function prompt_silent($prompt = "Enter Password:") {
        echo $prompt;
        return trim(fread( STDIN , 4096 )); 
    }
    function print_help()
    {
        $menu = [
            [
                "Command" => "/getpkey",
                "Description" => "Prints the generated public key in x509 format",
            ],
            [
                "Command" => "/private_enc {BASE64_DATA}",
                "Description" => "Encrypts base64-encoded data with private key and returns cipher in base64",
            ],
            [
                "Command" => "/public_enc  {BASE64_DATA}",
                "Description" => "Encrypts base64-encoded data with public key and returns cipher in base64",
            ],
            [
                "Command" => "/private_dec {BASE64_CIPHER}",
                "Description" => "Decrypts base64-encoded cipher data with private key",
            ],
            [
                "Command" => "/public_dec  {BASE64_CIPHER}",
                "Description" => "Decrypts base64-encoded cipher data with public key",
            ],
            [
                "Command" => "/file_private_enc [input] [output]",
                "Description" => "Encrypts a file with private key",
            ],
            [
                "Command" => "/file_public_enc [input] [output]",
                "Description" => "Encrypts a file with public key",
            ],
            [
                "Command" => "/file_private_dec [input] [output]",
                "Description" => "Decrypts a file with private key",
            ],
            [
                "Command" => "/file_public_dec [input] [output]",
                "Description" => "Decrypts a file with public key",
            ],
            [
                "Command" => "/dec_new_pkey",
                "Description" => "Decrypts base64-encoded cipher data with a a new public key in x509 format",
            ],
            [
                "Command" => "/enc_new_pkey",
                "Description" => "Encrypts base64-encoded data data with a a new public key in x509 format",
            ],
            [
                "Command" => "/file_dec_new_pkey [input] [output]",
                "Description" => "Decrypts a file with a new public key",
            ],
            [
                "Command" => "/file_enc_new_pkey [input] [output]",
                "Description" => "Encrypts a file with a new public key",
            ],
            [
                "Command" => "/help",
                "Description" => "Prints this help",
            ],
            [
                "Command" => "/exit",
                "Description" => "Exits the program",
            ],
        ];
        $m = new ArrayToTextTable( $menu );
        echo $m->render() . PHP_EOL;
    }
}




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



?>