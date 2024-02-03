<?php

namespace App\Tests\Unit;

use App\Entity\User;
use App\Entity\Dahra;
use PHPUnit\Framework\TestCase;

class SetGetDahraTest extends TestCase
{
    public function testGettersAndSetters()
    {
        $dahra = new Dahra();

        $dahra->setNom('Sahih bouhary');
        $this->assertEquals('Sahih bouhary', $dahra->getNom());

        $dahra->setAdresse('Dakar');
        $this->assertEquals('Dakar', $dahra->getAdresse());

        $dahra->setRegion('Dakar');
        $this->assertEquals('Dakar', $dahra->getRegion());

        $dahra->setNomOuztas('Bourama');
        $this->assertEquals('Bourama', $dahra->getNomOuztas());

        $dahra->setNumeroTelephoneOuztas('773548978');
        $this->assertEquals('773548978', $dahra->getNumeroTelephoneOuztas());

        $dahra->setNombreTalibe(30);
        $this->assertEquals(30, $dahra->getNombreTalibe());

        $this->assertNull($dahra->getId());
        $this->assertNull($dahra->getUser());
        $user = new User();
        $dahra->setUser($user);
        $this->assertEquals($user, $dahra->getUser());

      
    }
}
