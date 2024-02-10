<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class LoginTest extends WebTestCase
{
    public function testLogin()
    {
        $client = static::createClient();

        $data = [
            'email' => 'ngombourama18@gmail.com',
            'password' => 'password123',
        ];

        $client->request('POST', 'api/login', [], [], [], json_encode($data));

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $responseContent = json_decode($client->getResponse()->getContent(), true);

       
        $this->assertArrayHasKey('token', $responseContent);
        $this->assertNotEmpty($responseContent['token']);
    }
}
