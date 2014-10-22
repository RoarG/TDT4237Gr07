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
    
    /**
     * Check if is logged in.
     */
    static function check()
    {
        return isset($_SESSION['user']);
    }

    /**
     * Check if the person is a guest.
     */
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

    /**
     * Get currently logged in user.
     */
    static function user()
    {
        if (self::check()) {
            return User::findByUser($_SESSION['user']);
        }

        throw new \Exception('Not logged in but called Auth::user() anyway');
    }

    /**
     * Is currently logged in user admin?
     */
    static function isAdmin()
    {
        if (self::check()) {
            return $_COOKIE['isadmin'] === 'yes';
        }

        throw new \Exception('Not logged in but called Auth::isAdmin() anyway');
    }

    static function logout()
    {
        session_destroy();
    }
}
