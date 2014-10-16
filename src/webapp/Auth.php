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
        if ($token === $_SESSION['token']) {
            return true;
        }
        return false;
    }

    static function logIP() {
        return $_SERVER['REMOTE_ADDR'];
    }

    static function token() {
        if (isset($_SESSION['token'])) {
            return $_SESSION['token'];
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
