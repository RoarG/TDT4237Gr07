<?php

namespace tdt4237\webapp;

class Hash
{
    function __construct()
    {
    }

    static function make($plaintext)
    {
    	/**
    	 * Forklaring til password_hash funksjonen
    	 * We just want to hash our password using the current DEFAULT algorithm.
    	 * This is presently BCRYPT, and will produce a 60 character result.
    	 *
    	 * Beware that DEFAULT may change over time, so you would want to prepare
    	 * By allowing your storage to expand past 60 characters (255 would be good)
    	 */
    	
    	/**
    	 * Forstår det som at password_hash genererer en random salt og itererer hash
    	 * Må endre database variabel pass slik at den kan støtte flere tegn (satt den til 90)
    	 */
    	return password_hash($plaintext, PASSWORD_DEFAULT);
        //return hash('sha512', $plaintext);
    }

    static function check($plaintext, $hash)
    {
    	if(password_verify($plaintext, $hash)){
        	return password_verify($plaintext, $hash);
    	}else{
    		if(hash('sha512', $plaintext) === $hash){

    			$plaintext = hash('sha512', $plaintext);
                $diff = strlen($plaintext) ^ strlen($hash);
                for ($i=0; $i < strlen($plaintext) && $i < strlen($hash) ; $i++) {
                    $diff |= ord($plaintext[$i]) ^ ord($hash[$i]);
                }
                    return $diff === 0;
    		}		
    		return false;
    	}
        //return self::make($plaintext) === $hash;
    }
}
