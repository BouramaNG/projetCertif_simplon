<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = new User();
    }

    public function testEmail(): void
    {
        $email = "soly@gmail.com";
        $this->user->setEmail($email);
        $this->assertEquals($email, $this->user->getEmail());
    }

    public function testRoles(): void
    {
        $roles = ['ROLE_ADMIN'];
        $this->user->setRoles($roles);
    
        $expectedRoles = array_unique(array_merge($roles, ['ROLE_USER']));
        sort($expectedRoles);
    
        $userRoles = $this->user->getRoles();
        sort($userRoles); 
    
        $this->assertEquals($expectedRoles, $userRoles);
    }

    public function testPassword(): void
    {
        $password = "123456789";
        $this->user->setPassword($password);
        $this->assertEquals($password, $this->user->getPassword());
    }

    

}
