<?php

namespace App\Tests\Unit;

use App\Entity\Dahra;
use App\Entity\Talibe;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

class DonneTalibeTest extends TestCase
{
    public function testTalibeAppartientUneSeuleDahra()
    {
        $dahra = new Dahra();
        $talibe1 = new Talibe();
        $talibe1->setDahra($dahra);

        $talibe2 = new Talibe();
        $talibe2->setDahra($dahra);

        $this->assertSame($dahra, $talibe1->getDahra());
        $this->assertSame($dahra, $talibe2->getDahra());
    }

    public function testTalibeUnique()
    {
        $talibe1 = new Talibe();
        $talibe1->setNom('Bourama');
        $talibe1->setPrenom('Ngom');
        $talibe1->setAge(25);

        $talibe2 = new Talibe();
        $talibe2->setNom('Saliou');
        $talibe2->setPrenom('Diaw');
        $talibe2->setAge(25);

        $this->assertNotSame($talibe1, $talibe2);
    }

    // public function testNomPrenomString()
    // {
    //     $talibe = new Talibe();
    //     $talibe->setNom('Boula'); 
    //     $talibe->setPrenom('Boulama'); 

    //     $this->expectException(InvalidArgumentException::class);
    //     $this->expectExceptionMessage("Le nom et le prénom doivent être des chaînes de caractères");
    
      
    //     $this->validate($talibe);
    // }

    private function validate(Talibe $talibe)
    {
        $validator = Validation::createValidatorBuilder()
            ->enableAnnotationMapping()
            ->getValidator();

        return $validator->validate($talibe);
    }
}
