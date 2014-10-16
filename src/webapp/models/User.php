<?php

namespace tdt4237\webapp\models;

use tdt4237\webapp\Hash;

class User
{	
	
    const MIN_USER_LENGTH = 3;
    const MAX_USER_LENGTH = 20;

    protected $id = null;
    protected $user;
    protected $pass;
    protected $email;
    protected $bio = 'Bio is empty.';
    protected $age;
    protected $isAdmin = 0;

    static $app;

    function __construct()
    {
    }

    static function make($id, $username, $hash, $email, $bio, $age, $isAdmin)
    {
        $user = new User();
        $user->id = $id;
        $user->user = $username;
        $user->pass = $hash;
        $user->email = $email;
        $user->bio = $bio;
        $user->age = $age;
        $user->isAdmin = $isAdmin;

        return $user;
    }

    static function makeEmpty()
    {
        return new User();
    }

    /**
     * Insert or update a user object to db.
     */
    
    function save() {
        if ($this->id === null) {
        	$stmt = self::$app->db->prepare("INSERT INTO users(user, pass, email, age, bio, isadmin) VALUES(?, ?, ?, ?, ?, ?)");
            $stmt->execute(array($this->user, $this->pass, $this->email, $this->age, $this->bio, $this->isAdmin));
        }
        else {
        	$stmt = self::$app->db->prepare("UPDATE users SET email=?, age=?, bio=?, isadmin=? WHERE id=?");
            $stmt->execute(array($this->email, $this->age, $this->bio, $this->isAdmin, $this->id));
        }
    }

    function getId()
    {
        return $this->id;
    }

    function getUserName()
    {
        return $this->user;
    }

    function getPasswordHash()
    {
        return $this->pass;
    }

    function getEmail()
    {
        return $this->email;
    }

    function getBio()
    {
        return $this->bio;
    }

    function getAge()
    {
        return $this->age;
    }

    function isAdmin()
    {
        return $this->isAdmin === "1";
    }

    function setId($id)
    {
        $this->id = $id;
    }

    function setUsername($username)
    {
        $this->user = $username;
    }

    function setHash($hash)
    {
        $this->pass = $hash;
    }

    function setEmail($email)
    {
        $this->email = $email;
    }

    function setBio($bio)
    {
        $this->bio = $bio;
    }

    function setAge($age)
    {
        $this->age = $age;
    }

    /**
     * The caller of this function can check the length of the returned 
     * array. If array length is 0, then all checks passed.
     *
     * @param User $user
     * @return array An array of strings of validation errors
     */
    static function validate(User $user)
    {
        $validationErrors = [];

        if (strlen($user->user) < self::MIN_USER_LENGTH) {
            array_push($validationErrors, "Username too short. Min length is " . self::MIN_USER_LENGTH);
        }
        if(strlen($user->user) > self::MAX_USER_LENGTH){
        	array_push($validationErrors, "Username too long. Max lenght is " . self::MAX_USER_LENGTH);
        }
        

        if (preg_match('/^[A-Za-z0-9]+$/', $user->user) === 0) {
            array_push($validationErrors, 'Username can only contain letters and numbers');
        }

        return $validationErrors;
    }

    static function validateAge(User $user)
    {
        $age = $user->getAge();

        if ($age >= 0 && $age <= 150) {
            return true;
        }

        return false;
    }

    /**
     * Find user in db by username.
     *
     * @param string $username
     * @return mixed User or null if not found.
     */
    static function findByUser($username)
    {   
    	$stmt = self::$app->db->prepare("SELECT * FROM users WHERE user=?");
        $stmt->execute(array($username));
        $row = $stmt->fetch();

        if($row == false) {
            return null;
        }
        return User::makeFromSql($row);
    }

    static function deleteByUsername($username)
    {
    	$stmt = self::$app->db->prepare("DELETE FROM users WHERE user=?");
        return $stmt->execute(array($username));
    }

    static function all()
    {	
    	$stmt = self::$app->db->prepare("SELECT * FROM users");
        $stmt->execute();
        $results = $stmt->fetchAll();
        $users = [];

        foreach ($results as $row) {
            $user = User::makeFromSql($row);
            array_push($users, $user);
        }

        return $users;
    }

    static function makeFromSql($row)
    {
        return User::make(
            $row['id'],
            $row['user'],
            $row['pass'],
            $row['email'],
            $row['bio'],
            $row['age'],
            $row['isadmin']
        );
    }
}
User::$app = \Slim\Slim::getInstance();
