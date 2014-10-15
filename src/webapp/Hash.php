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
    	 * Forst�r det som at password_hash genererer en random salt og itererer hash
    	 * M� endre database variabel pass slik at den kan st�tte flere tegn (satt den til 90)
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
    			return true;
    		}		
    		return false;
    	}
        //return self::make($plaintext) === $hash;
    }
}
