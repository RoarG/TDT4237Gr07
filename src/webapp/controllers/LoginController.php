<?php

namespace tdt4237\webapp\controllers;

use tdt4237\webapp\Auth;

class LoginController extends Controller
{	
    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        if (Auth::check()) {
            $username = Auth::user()->getUserName();
            $this->app->flash('info', 'You are already logged in as ' . $username);
            $this->app->redirect('/');
        } else {
            $this->render('login.twig', ['token' => Auth::token()]);
        }
    }
    
    function login()
    {   
        if (Auth::checkToken($this->app->request->post('CSRFToken'))) {

            $request = $this->app->request;
            $user = $request->post('user');
            $pass = $request->post('pass');

            if (Auth::checkCredentials($user, $pass)) {
                
                session_regenerate_id();
                $_SESSION['user'] = $user;

                $isAdmin = Auth::user()->isAdmin();

                $this->app->flash('info', "You are now successfully logged in as $user.");
                $this->app->redirect('/');
            }
            else {
                $this->app->flashNow('error', 'Incorrect user/pass combination.');
                $this->render('login.twig', ['token' => Auth::token()]);
            }
        }
        else {
            $this->app->flashNow('error', "This page has timed out, please try again!");
            $this->render('login.twig', ['token' => Auth::token()]);
        }
    }
}
