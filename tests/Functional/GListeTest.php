<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ListeTest extends WebTestCase
{
    public function testListerTalibe()
    {
        $client = static::createClient();

    
        $client->request('GET', 'api/lister-talibe');

        
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));

        
        $responseContent = json_decode($client->getResponse()->getContent(), true);

        
        $this->assertNotEmpty($responseContent);

        
        foreach ($responseContent as $talibe) {
            $this->assertArrayHasKey('id', $talibe);
            $this->assertArrayHasKey('prenom', $talibe);
            $this->assertArrayHasKey('nom', $talibe);
            $this->assertArrayHasKey('age', $talibe);
            $this->assertArrayHasKey('adresse', $talibe);
            $this->assertArrayHasKey('situation', $talibe);
            $this->assertArrayHasKey('description', $talibe);
            $this->assertArrayHasKey('image', $talibe);
            $this->assertArrayHasKey('datearrivetalibe', $talibe);
            $this->assertArrayHasKey('dahraNom', $talibe);
        }
    }
}
