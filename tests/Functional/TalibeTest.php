<?php

namespace App\Tests\Functional;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class TalibeTest extends WebTestCase
{
    public function testAddTalibeToDahra()
    {
        $client = static::createClient(['environment' => 'test']);

        $data = [
            'nom' => 'Talibe Nom',
            'prenom' => 'Talibe Prenom',
            'age' => 12,
            'adresse' => 'Adresse Talibe',
            'situation' => 'Situation Talibe',
            'description' => 'Description Talibe',
            'datearrivetalibe' => '2024-01-22',
            'presencetalibe' => 'present',
            
        ];

        $client->request('POST', '/api/dahra/add-talibe', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode($data));

        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('talibeId', $responseData);
        $this->assertNotEmpty($responseData['talibeId']);
       
    }
}
