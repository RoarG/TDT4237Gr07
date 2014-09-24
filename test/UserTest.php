<?php

use tdt4237\webapp\models\User;

class UserTest extends \PHPUnit_Framework_TestCase
{
    function setUp()
    {
        $this->user = User::make(5, 'luckylucke', '', '', '', '', false);
    }

    function testUser()
    {
        $user = $this->user;

        $this->assertEquals($user->getId(), 5);
        $this->assertEquals($user->getUserName(), 'luckylucke');
        $this->assertFalse($user->isadmin());

        $user->setId(1337);
        $this->assertEquals($user->getId(), 1337);
    }

    function testValidate()
    {
        $user = $this->user;
        $errors = User::validate($user);
        $this->assertEquals(sizeof($errors), 0);
    }
}
