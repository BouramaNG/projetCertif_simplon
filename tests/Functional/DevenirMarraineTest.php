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
            'nom' => 'bourama',
            'prenom' => 'bourama',
            'email' => 'jane.doe@example.com',
            'password' => 'strongpassword',
            'adresse' => '321 rue Liberté',
            'numeroTelephone' => '9876543210',
        ];

        $client->request('POST', '/api/devenir-marraine', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($data));

        $this->assertResponseIsSuccessful();
        $this->assertEquals(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('Inscription Marraine|Parraine avec succès', $responseData['message']);
    }
}
