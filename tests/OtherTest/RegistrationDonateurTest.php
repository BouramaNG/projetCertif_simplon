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
            'email' => 'boura@gmail.com',
            'password' => 'password123',
            'adresse' => 'Pikine',
            'numeroTelephone' => '783718472',
        ];

        $client->request('POST', '/api/inscrire-donateur', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($data));

        $this->assertResponseIsSuccessful();
        $this->assertEquals(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('Inscription Donateur avec succès', $responseData['message']);
    }
}
