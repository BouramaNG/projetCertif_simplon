<?php

namespace App\Tests\Functional;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DevenirMarraineTest extends WebTestCase
{
    public function testDevenirMarraine()
    {
        $client = static::createClient();

        $data = [
            'nom' => 'boura',
            'prenom' => 'bounama',
            'email' => 'bourama12@gmail.com',
            'password' => 'password123',
            'adresse' => 'dakar',
            'numeroTelephone' => '783718472',
        ];

        $client->request('POST', '/api/devenir-marraine', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($data));

        $this->assertResponseIsSuccessful();
        $this->assertEquals(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('Inscription Marraine|Parraine avec succès', $responseData['message']);
    }
}
