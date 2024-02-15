<?php

namespace App\Tests\Unit;

use App\Entity\User;
use App\Entity\Newsletter;
use PHPUnit\Framework\TestCase;

class NewsletterTest extends TestCase
{
    public function testCreationNewsletter()
    {
        $newsletter = new Newsletter();
        $this->assertInstanceOf(Newsletter::class, $newsletter);
    }

    public function testSetGetEmail()
    {
        $newsletter = new Newsletter();
        $email = 'ngombourama@gmail.com';
        $newsletter->setEmail($email);
        $this->assertEquals($email, $newsletter->getEmail());
    }

    public function testSetGetCreatedAt()
    {
        $newsletter = new Newsletter();
        $createdAt = new \DateTime();
        $newsletter->setCreatedAt($createdAt);
        $this->assertEquals($createdAt, $newsletter->getCreatedAt());
    }

    public function testSetGetUser()
    {
        $newsletter = new Newsletter();
        $user = new User();
        $newsletter->setUser($user);
        $this->assertSame($user, $newsletter->getUser());
    }
}
