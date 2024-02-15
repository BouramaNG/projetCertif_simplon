<?php

namespace App\Tests\Entity;

use App\Entity\Parrainage;
use App\Entity\Talibe;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class ParrainageTest extends TestCase
{
    private Parrainage $parrainage;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parrainage = new Parrainage();
    }

    public function testDate(): void
    {
        $date = new \DateTime();
        $this->parrainage->setDate($date);
        $this->assertEquals($date, $this->parrainage->getDate());
    }

    public function testTypeParrainage(): void
    {
        $typeParrainage = 'ndeyeDahra';
        $this->parrainage->setTypeParrainage($typeParrainage);
        $this->assertEquals($typeParrainage, $this->parrainage->getTypeParrainage());
    }

    public function testTypeParrainageInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->parrainage->setTypeParrainage('invalidType');
    }

    public function testStatus(): void
    {
        $status = 'valide';
        $this->parrainage->setStatus($status);
        $this->assertEquals($status, $this->parrainage->getStatus());
    }

    public function testStatusInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->parrainage->setStatus('invalidStatus');
    }

    // Test pour Talibe
    public function testTalibe(): void
    {
        $talibe = new Talibe();
        $this->parrainage->setTalibe($talibe);
        $this->assertSame($talibe, $this->parrainage->getTalibe());
    }

    // Test pour User
    public function testUser(): void
    {
        $user = new User();
        $this->parrainage->setUser($user);
        $this->assertSame($user, $this->parrainage->getUser());
    }

}
