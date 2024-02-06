<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ListeDahraTest extends WebTestCase
{
    public function testListerDahra()
    {
        $client = static::createClient();

        $client->request('GET', 'api/lister-dahra');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));

        $responseContent = json_decode($client->getResponse()->getContent(), true);

 
        $this->assertNotEmpty($responseContent);

        foreach ($responseContent as $dahra) {
            $this->assertArrayHasKey('id', $dahra);
            $this->assertArrayHasKey('nom', $dahra);
            $this->assertArrayHasKey('adresse', $dahra);
            $this->assertArrayHasKey('region', $dahra);
            $this->assertArrayHasKey('nombreTalibe', $dahra);
            $this->assertArrayHasKey('nomOuztas', $dahra);
            $this->assertArrayHasKey('numeroTelephoneOuztas', $dahra);
        }
    }
}
