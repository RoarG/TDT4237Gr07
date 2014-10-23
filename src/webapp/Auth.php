<?php

namespace tdt4237\webapp;

use tdt4237\webapp\models\User;
use tdt4237\webapp\Hash;

class Auth
{
    function __construct()
    {
    }

    static function checkCredentials($username, $password)
    {
        $user = User::findByUser($username);

        if ($user === null) {
            return false;
        }

        return Hash::check($password, $user->getPasswordHash());
    }

    static function generateToken() {
        $token = base64_encode(openssl_random_pseudo_bytes(32));
        return $token;
    }

    static function checkToken($token) {
        if ($token == $_SESSION['token']) {
            return true;
        }
        return false;
    }

    static function token() {
        if (isset($_SESSION['token'])) {
            return $_SESSION['token'];
        }
    }

    static function generatePseudoRandom($length) {
        $chars = "ABCDEFGHIJKLMNOPQRSTUWXYZabcdefghijklmnopqrstuwxyz1234567890-_*@!$%&()=?";
        $str = "";
        for ($i=0; $i < $length; $i++) {
            $str .= $chars[rand(0,strlen($chars)-1)];
        }
        return $str;
    }

    static function checkEmail($username, $email){
    	$user = User::findByUser($username);
    	
    	if($user === null){
    		return false;
    	}else{
    		if($user->getEmail() === $email){
    			return true;
    		}
    		return false;
    	}
    }
    
    static function check()
    {
        return isset($_SESSION['user']);
    }

    static function guest()
    {
        return self::check() === false;
    }
    
    static function resetPass(){
    	if(isset($_SESSION['reset'])){
    		if(isset($_SESSION['timeout']) && (time() - $_SESSION['timeout'] > 600)){
    			$user = User::findByUser($_SESSION['reset']);
    			$user->setCode(null);
    			session_unset();
    			session_destroy();
    			return null;
    		}
    		return User::findByUser($_SESSION['reset']);
    	}
    	return null;
    }

    static function user()
    {
        if (self::check()) {
            return User::findByUser($_SESSION['user']);
        }

        throw new \Exception('Not logged in but called Auth::user() anyway');
    }

    static function isAdmin()
    {
        if (self::check()) {
            return Auth::user()->isAdmin();
        }
        throw new \Exception('Not logged in but called Auth::isAdmin() anyway');
    }

    static function logout()
    {
        session_destroy();
    }
}
