<?php

namespace App\Tests\Functional;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FaireDonTest extends WebTestCase
{
    public function testFaireDon()
    {
        $client = static::createClient();

      

        $data = [
            'status' => 'en attente',
            'typeDon' => 'Nourriture',
            'adresseProvenance' => 'Mariste',
            'descriptionDon' => 'pour la nouriture des talibe',
            'disponibiliteDon' => 'immediate',
            'dahra_name' => 'Sahih boukhary'
        ];

        $client->request('POST', '/api/faire-don', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($data));

        $this->assertResponseIsSuccessful();
        $this->assertEquals(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('Don effectué avec succès', $responseData['message']);
    }
}
