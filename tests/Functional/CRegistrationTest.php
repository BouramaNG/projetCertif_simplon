<?php

namespace App\Tests\Functional;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DevenirDahraTest extends WebTestCase
{
    public function testRegisterDahra()
    {


        
        $client = static::createClient();

       
        $data = [
            'email' => 'ngombourama100@gmail.com',
            'password' => 'password123',
            'numeroTelephone' => '771234567',
            'nom' => 'sahih Bouhary',
            'nomOuztas' => 'Bouhary',
            'adresse' => 'Dakar',
            'region' => 'Dakar',
            'numeroTelephoneOuztas' => '773456789',
            'nombreTalibe' => 100
        ];

     
        $client->request(
            'POST',
            '/api/dahra',
            [],
            [], 
            [], 
            json_encode($data) 
            
        );
        
     
        $this->assertResponseStatusCodeSame(201); 

        // Vérifiez le contenu de la réponse JSON
        $this->assertJson($client->getResponse()->getContent());
        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals($data['nom'], $responseData['nom']);
    }

    public function testRegisterDonateur()
    {
        $client = static::createClient();

        $data = [
            'nom' => 'bourama',
            'prenom' => 'bourama',
            'email' => 'bourama300@gmail.com',
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

    public function testDevenirMarraine()
    {
        $client = static::createClient();

        $data = [
            'nom' => 'boura',
            'prenom' => 'bounama',
            'email' => 'bourama140@gmail.com',
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
