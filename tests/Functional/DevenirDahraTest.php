<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DevenirDahraTest extends WebTestCase
{
    public function testRegisterDahra()
    {
        $client = static::createClient();

        $data = [
            'email' => 'ngombourama18@gmail.com',
            'password' => 'password123',
            'numeroTelephone' => '771234567',
            'nom' => 'sahih Bouhary',
            'nomOuztas' => 'Bouhary',
            'adresse' => 'Dakar',
            'region' => 'Dakar',
            'numeroTelephoneOuztas' => '773456789',
            'nombreTalibe' => 100
        ];

        $client->request('POST', '/api/register/dahra', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($data));

        $this->assertResponseStatusCodeSame(201); 
        $this->assertJson($client->getResponse()->getContent());
        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals($data['nom'], $responseData['nom']);
      
    }
}
