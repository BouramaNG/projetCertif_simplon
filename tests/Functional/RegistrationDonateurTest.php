<?php

namespace App\Tests\Functional;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegistrationDonateurTest extends WebTestCase
{
    public function testRegisterDonateur()
    {
        $client = static::createClient();

        $data = [
            'nom' => 'bourama',
            'prenom' => 'bourama',
            'email' => 'jean.dupont@example.com',
            'password' => 'password123',
            'adresse' => '123 rue de la Paix',
            'numeroTelephone' => '0123456789',
        ];

        $client->request('POST', '/api/inscrire-donateur', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($data));

        $this->assertResponseIsSuccessful();
        $this->assertEquals(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('Inscription Donateur avec succès', $responseData['message']);
    }
}
