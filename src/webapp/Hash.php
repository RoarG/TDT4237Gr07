<?php

namespace tdt4237\webapp;

class Hash
{
    function __construct()
    {
    }

    static function make($plaintext)
    {
        return hash('sha512', $plaintext);
    }

    //Time constant compare
    static function check($plaintext, $hash)
    {
        $plaintext = self::make($plaintext);
        $diff = strlen($plaintext) ^ strlen($hash);
        for ($i=0; $i < strlen($plaintext) && $i < strlen($hash) ; $i++) {
            $diff |= ord($plaintext[$i]) ^ ord($hash[$i]);
        }
        return $diff === 0;
    }
}

