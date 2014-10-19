<?php

namespace tdt4237\webapp\controllers;

use tdt4237\webapp\models\User;
use tdt4237\webapp\Hash;
use tdt4237\webapp\Auth;

class UserController extends Controller
{
    function __construct()
    {
        parent::__construct();
    }

    function index()
    {
        if (Auth::guest()) {
            $this->render('newUserForm.twig', ['token' => Auth::token()]);
        } else {
            $username = Auth::user()->getUserName();
            $this->app->flash('info', 'You are already logged in as ' . $username);
            $this->app->redirect('/');
        }
    }
    
    function validatePass($pass){
    	$validationErrors = [];
    	$minpass = 8;
    	$maxpass = 25;
    	
    	/*Dumt � skrive inn passordfeil her?*/
    	if(strlen($pass) < $minpass){
    		array_push($validationErrors, "Password too short. Min length is " . $minpass);
    	}
    	if(strlen($pass) > $maxpass){
    		array_push($validationErrors, " Password too long. Max length is " . $maxpass);
    	}
    	
        return $validationErrors;
    }

    function create()
    {   
        if (Auth::checkToken($this->app->request->post('CSRFToken'))) {
            $request = $this->app->request;
            $username = $request->post('user');
            $pass = $request->post('pass');

            $hashed = Hash::make($pass);

            $user = User::makeEmpty();
            $user->setUsername($username);
            $user->setHash($hashed);

            $validationError = User::validate($user);
            $validationError2 = self::validatePass($pass);
            $result = array_merge($validationError,$validationError2);

            if (sizeof($result) > 0) {
                $errors = join("<br>\n", $result);
                $this->app->flashNow('error', $errors);
                $this->render('newUserForm.twig', ['username' => $username, 'token' => Auth::token()]);
            } else {
                $user->save();
                $this->app->flash('info', 'Thanks for creating a user. Now log in.');
                $this->app->redirect('/login');
            }
        }
        else {
            $this->app->flash('error', 'This page has timed out, please try again!');
            $this->render('newUserForm.twig', ['token' => Auth::token()]);
        }
    }

    function all()
    {
        $users = User::all();
        $this->render('users.twig', ['users' => $users]);
    }

    function logout()
    {
        Auth::logout();
        $this->app->redirect('/?msg=Successfully logged out.');
    }

    function show($username)
    {
        $user = User::findByUser($username);

        $this->render('showuser.twig', [
            'user' => $user,
            'username' => $username
        ]);
    }

    function edit()
    {
        if (Auth::guest()) {
            $this->app->flash('info', 'You must be logged in to edit your profile.');
            $this->app->redirect('/login');
            return;
        }

        $user = Auth::user();

        if (! $user) {
            throw new \Exception("Unable to fetch logged in user's object from db.");
        }

        if ($this->app->request->isPost()) {
            $request = $this->app->request;
            if (Auth::checkToken($request->post('CSRFToken'))) {
                $email = $request->post('email');
                $bio = $request->post('bio');
                $age = $request->post('age');

                $user->setEmail($email);
                $user->setBio($bio);
                $user->setAge($age);

                if (! User::validateAge($user)) {
                    $this->app->flashNow('error', 'Age must be between 0 and 150.');
                } else {
                    $user->save();
                    $this->app->flashNow('info', 'Your profile was successfully saved.');
                }
            }
            else {
                $this->app->flashNow('error', "This page has timed out, please try again!");
            }
        }
        $this->render('edituser.twig', array('user' => $user, 'token' => Auth::token()));
    }
}
