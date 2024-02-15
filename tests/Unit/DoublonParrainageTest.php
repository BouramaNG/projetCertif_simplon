<?php

namespace App\Tests\Unit;

use App\Entity\Talibe;
use App\Entity\Parrainage;
use PHPUnit\Framework\TestCase;

class DoublonParrainageTest extends TestCase
{
    public function testParrainageUniquePourTalibe()
{
    
    $talibe = new Talibe();

    $parrainage1 = new Parrainage();
    $parrainage1->setTalibe($talibe);

    $parrainage2 = new Parrainage();
    $parrainage2->setTalibe($talibe);

    $this->assertNotSame($parrainage1, $parrainage2);
}
}
