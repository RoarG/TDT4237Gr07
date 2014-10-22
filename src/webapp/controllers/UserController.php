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
    	
    	if(strlen($pass) < $minpass){
    		array_push($validationErrors, "Password too short. Min length is " . $minpass);
    	}
    	if(strlen($pass) > $maxpass){
    		array_push($validationErrors, " Password too long. Max length is " . $maxpass);
    	}

    	if (! preg_match ( '/[A-Za-z]/', $pass )) {
    		array_push ( $validationErrors, 'Password must contain letters' );
    	}
    	if (! preg_match ( '/[0-9]/', $pass )) {
    		array_push ( $validationErrors, 'Password must contain numbers' );
    	}
    	if (! preg_match ( '/[_\W]/', $pass )) {
    		array_push ( $validationErrors, 'Password must contain special characters' );
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
            $user->setEmail($email);

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
    
    
    function forgot(){
    	if (Auth::check()) {
    		$username = Auth::user()->getUserName();
    		$this->app->flash('info', 'You are already logged in as ' . $username);
    		$this->app->redirect('/');
    	} else {
    		$this->render('forgotPass.twig', []);
    	}
    }
    
    function reset(){
    	$request = $this->app->request;
    	$username = $request->post('user');
    	$email = $request->post('email');
    	 
    	//Sett XSS test på dette!!
    		if(Auth::checkEmail($username, $email)){
    			$user = User::findByUser($username);
    			$code = self::generateRandomString();
    			$user->setCode($code);
    			$user->save();
    			$_SESSION['reset'] = $user->getUsername();
    			$_SESSION['timeout'] = time();
    			$this->app->redirect('/reset/validate');
    		}else{
    			$this->app->flashNow('error', 'Incorrect user/email combination.');
    			$this->render('forgotPass.twig', []);
    		}
    }
    
    function validate(){
    	$request = $this->app->request;
    	$validation = $request->post('val');
    	$newpass = $request->post('newpass');
    	$user = Auth::resetPass();
    	if (Auth::check()) {
    		$username = Auth::user()->getUserName();
    		$this->app->flash('info', 'You are already logged in as ' . $username);
    		$this->app->redirect('/');
    	}elseif(isset($user)){
    		$code = self::generateRandomString();
    		$code = $user->getCode();
    		if(!isset($validation)){
    			$this->render('resetPass.twig', []);    		
    		}elseif(isset($_SESSION['timeout']) && (time() - $_SESSION['timeout'] > 600)){
    			$user->setCode(null);
    			$user->save();
    			session_unset();
    			session_destroy();
    			$this->app->flash('info', 'Timeout');
    			$this->app->redirect('/login');
    		}elseif($validation === $code){
    			$validationError = self::validatePass($newpass);
    			$hashed = Hash::make($newpass);
    			$user->setHash($hashed);
    			if (sizeof($validationError) > 0) {
    				$errors = join("<br>\n", $validationError);
    				$this->app->flashNow('error', $errors);
    				$this->render('resetPass.twig', []);
    			} else {
    				$user->setCode(null);
    				$user->save();
    				$this->app->flash('info', 'Your password has been reset. Now log in.');
    				$this->app->redirect('/login');
    				session_unset();
    				session_destroy();
    			}
    		}else {
    			$this->app->flashNow('error', 'Incorrect validationcode.');
    			$this->render('resetPass.twig', []);
    		}
    	}else{
    		$this->app->redirect('/');
    	}
    }
    
    function mail(){
    	$user = Auth::resetPass();
    	if (Auth::check()) {
    		$username = Auth::user()->getUserName();
    		$this->app->flash('info', 'You are already logged in as ' . $username);
    		$this->app->redirect('/');
    	}elseif(isset($user)){
    		$email = $user->getEmail();
    		$code = $user->getCode();
    		$this->render('mail.twig',['code'=>$code, 'email' =>$email]);
    	}else{
    		$this->app->redirect('/');
    	}
    }
    
    function generateRandomString() {
    	$length = 15;
    	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    	$randomString = '';
    	for ($i = 0; $i < $length; $i++) {
    		$randomString .= $characters[rand(0, strlen($characters) - 1)];
    	}
    	return $randomString;
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

    static function validateImage() {
        $fileName = $_FILES['file']['name'];
        $validFiletypes = array('jpg', 'jpeg', 'gif', 'png');
        $fileExtension = explode(".", $fileName);

        if (substr_count($fileName, ".") > 1) {
            return "The filename contains more than one .";
        }

        if (! getimagesize($_FILES['file']['tmp_name'])[0]) {
            return "The file is not an image.";
        }
        
        $exists = false;
        foreach ($validFiletypes as $filetype) {
            if ($filetype == $fileExtension[1]) {
                $exists = true;
                break; 
            }
        }
        if (! $exists) {
            return "The file is of an unsupported format.";
        }

        if ($_FILES['file']['size'] > 10000000) {
                return "The file is too large.";
        }
        
        return true;
    } 

    //saves image to image folder and returns the name of the file as it is saved in that folder
    static function saveImage() {
        if (! file_exists($_SERVER['DOCUMENT_ROOT'] . "/images/profilepics/")) {
            mkdir($_SERVER['DOCUMENT_ROOT'] . "/images/profilepics/");
        }
        $uploadedFilename = $_FILES['file']['name'];
        $exploded = explode(".", $uploadedFilename);
        $filetype = $exploded[1];
        $filenameToSave = Auth::generatePseudoRandom(8) . "." . $filetype;
        $fileToSave = $_FILES['file']['tmp_name'];
        move_uploaded_file($fileToSave, $_SERVER['DOCUMENT_ROOT'] . "/images/profilepics/" . $filenameToSave);
        return $filenameToSave;
    }

    static function getProfilePicURL($username) {
        $user = User::findByUser($username);
        return $user->getImageUrl();
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
                } 
                else {
                    if ($_FILES['file']['name'] != null) {
                        $response = UserController::validateImage();

                        if ($response === true) {
                            $imageUrl = self::saveImage();
                            $user->setImageUrl($imageUrl);
                        }
                        else {
                            $this->app->flashNow('error', $response);
                        }     
                    }
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
  
