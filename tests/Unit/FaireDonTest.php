<?php

namespace App\Tests\Entity;

use App\Entity\Dahra;
use App\Entity\FaireDon;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class FaireDonTest extends TestCase
{
    public function testCreateFaireDon()
    {
        $faireDon = new FaireDon();

        $this->assertInstanceOf(FaireDon::class, $faireDon);
    }

    public function testSetAndGetStatus()
    {
        $faireDon = new FaireDon();
        $status = "En attente";

        $faireDon->setStatus($status);
        $this->assertEquals($status, $faireDon->getStatus());
    }

    public function testSetAndGetDahra()
    {
        $faireDon = new FaireDon();
        $dahra = new Dahra();
        $dahra->setNom("Nom de Dahra");

        $faireDon->setDahra($dahra);
        $this->assertInstanceOf(Dahra::class, $faireDon->getDahra());
    }

    public function testSetAndGetUser()
    {
        $faireDon = new FaireDon();
        $user = new User();
        $user->setNom("Bourama");

        $faireDon->setUser($user);
        $this->assertInstanceOf(User::class, $faireDon->getUser());
    }


}
