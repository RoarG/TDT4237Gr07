<?php

namespace tdt4237\webapp\controllers;
use tdt4237\webapp\Auth;

class Controller
{
    protected $app;

    function __construct()
    {
        $this->app = \Slim\Slim::getInstance();
    }

    function render($template, $variables = [])
    {
        if (! Auth::guest()) {
            $variables['isLoggedIn'] = true;
            $variables['isAdmin'] = Auth::isAdmin();
            $variables['loggedInUsername'] = $_SESSION['user'];
            $variables['profilePicUrl'] = UserController::getProfilePicUrl($_SESSION['user']);
        }

        if (! isset($_SESSION['token'])) {
            $_SESSION['token'] = Auth::generateToken();
        }


        print $this->app->render($template, $variables);
    }
}
