<?php

namespace App\Tests\Functional;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class TalibeTest extends WebTestCase
{
    public function testAddTalibeToDahra()
    {
        $client = static::createClient();

        $data = [
            'nom' => 'NomTest',
            'prenom' => 'PrenomTest',
            'age' => 25,
            'dahra_id' => 1,
            'adresse' => 'AdresseTest',
            'situation' => 'SituationTest',
            'description' => 'DescriptionTest',
            'image' => 'ImageTest.jpg',
            'datearrivetalibe' => '2022-01-01', // Assurez-vous que cette clé correspond à ce que votre action attend
            'presencetalibe' => 'present',
        ];

        $client->request('POST', 'api/add-talibeTest', [], [], [], json_encode($data));

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $responseContent = json_decode($client->getResponse()->getContent(), true);

        // Assurez-vous que la réponse contient le message attendu et l'ID du Talibe
        $this->assertArrayHasKey('message', $responseContent);
        $this->assertArrayHasKey('talibeId', $responseContent);
        $this->assertEquals('Choukrane vous avez ajouté avec succès un Talibe !', $responseContent['message']);
        $this->assertNotEmpty($responseContent['talibeId']);
    }
}
