<?php

namespace App\Tests\Entity;

use App\Entity\Dahra;
use App\Entity\Talibe;
use App\Entity\FaireDon;
use PHPUnit\Framework\TestCase;

class DahraUnitTest extends TestCase
{
    private Dahra $dahra;

    protected function setUp(): void
    {
        $this->dahra = new Dahra();
    }

    public function testGetId()
    {
        $this->assertNull($this->dahra->getId());
    }

    public function testNom()
    {
        $nom = "Nom de la Dahra";
        $this->dahra->setNom($nom);
        $this->assertEquals($nom, $this->dahra->getNom());
    }

    public function testAdresse()
    {
        $adresse = "Adresse de la Dahra";
        $this->dahra->setAdresse($adresse);
        $this->assertEquals($adresse, $this->dahra->getAdresse());
    }

    public function testRegion()
    {
        $region = "Région de la Dahra";
        $this->dahra->setRegion($region);
        $this->assertEquals($region, $this->dahra->getRegion());
    }

    public function testNomOuztas()
    {
        $nomOuztas = "Nom Ouztas";
        $this->dahra->setNomOuztas($nomOuztas);
        $this->assertEquals($nomOuztas, $this->dahra->getNomOuztas());
    }

    public function testNumeroTelephoneOuztas()
    {
        $numero = "123456789";
        $this->dahra->setNumeroTelephoneOuztas($numero);
        $this->assertEquals($numero, $this->dahra->getNumeroTelephoneOuztas());
    }

    public function testAddRemoveTalibe()
    {
        $talibe = new Talibe();
        $this->dahra->addTalibe($talibe);
        $this->assertCount(1, $this->dahra->getTalibes());

        $this->dahra->removeTalibe($talibe);
        $this->assertCount(0, $this->dahra->getTalibes());
    }

    public function testAddRemoveFaireDon()
    {
        $faireDon = new FaireDon();
        $this->dahra->addFaireDon($faireDon);
        $this->assertCount(1, $this->dahra->getFaireDons());

        $this->dahra->removeFaireDon($faireDon);
        $this->assertCount(0, $this->dahra->getFaireDons());
    }
}
