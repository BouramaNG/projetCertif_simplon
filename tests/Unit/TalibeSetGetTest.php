<?php

namespace App\Tests\Unit;

use App\Entity\Talibe;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TalibeSetGetTest extends KernelTestCase
{
    public function testGettersAndSetters()
    {
        $talibe = new Talibe();
        $talibe->setNom('Bourama');
        $this->assertEquals('Bourama', $talibe->getNom());
    
        $talibe->setPrenom('Ngom');
        $this->assertEquals('Ngom', $talibe->getPrenom());
    
        $talibe->setAge(25);
        $this->assertEquals(25, $talibe->getAge());
    
        $talibe->setAdresse('Hann Mariste');
        $this->assertEquals('Hann Mariste', $talibe->getAdresse());
    
        $talibe->setSituation('Single');
        $this->assertEquals('Single', $talibe->getSituation());
    
        $talibe->setDescription('Some description');
        $this->assertEquals('Some description', $talibe->getDescription());
    
        $talibe->setImage('image.jpg');
        $this->assertEquals('image.jpg', $talibe->getImage());
        $this->assertEquals('present', $talibe->getPresenceTalibe());
        $this->expectException(\InvalidArgumentException::class);
        $talibe->setPresenceTalibe('invalid_status');
    }
}
