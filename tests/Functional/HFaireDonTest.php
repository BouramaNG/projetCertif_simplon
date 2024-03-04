<?php

namespace App\Tests\Functional;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FaireDonTest extends WebTestCase
{
    // public function testFaireDon()
    // {
    //     $client = static::createClient();
    
    //     $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE3MDczMDYyMjAsImV4cCI6MTcwNzMwOTgyMCwicm9sZXMiOlsiUk9MRV9EQUhSQSIsIlJPTEVfVVNFUiJdLCJ1c2VybmFtZSI6Im5nb21ib3VyYW1hNjVAZ21haWwuY29tIn0.hL7J79ICNJ9wYb6eCBqgbSb5HNQaZGN89_J0tH3i6U_36p6xcy0Zvw9s6OK5culax5_kBdVSFL_rjpOMotLw4pJBBpqK8y-oqjzNJUmdv5SojqtKzBMDu6tXZAD5aIm3diANSgwnQzOHfUDfzMXctagRhgcgxE9ritsp4dqft2wUkKHS3aGEL2JDY1uDPNX2nJovLUWo4HI2GiKaya2hWjlZQYDDRGtMD7GJ1AqhSKYbthraSt-Hh3wC-D8n-tZSodiHFvSfi3Jyn_VE3r6rlkQbCXsgxzd7lKNwzni7VyWZKz_WNXY-vuOdRW52A5BSDV8VtAkIAYmG78gFV0j6WGqb1ksrvudwjNMyH0woUqFb1wZ-m-lxqGByNUOLTY369G6YVINXQD8gPKJwM1ZkO1At5IP0Fe2n2RhXgRW3dBcbVHp0fQ9uZjZ58CeYDuPhQoPJ-DqchLpIkrdoiwkKg2GTsNbzHSS3remBtCibmS1cVMHjUKB2g7gSfOOLX-y-nLbmkLajJxWd7Z9Dg05izlpr39E5vcK6dwam3SfB3fowjjjWUDuTuDd14spTzCUY0G0bnZl2MvRe7mX3rXEu_KNCLblsjC8wJ-9cB8D6KZ-5KhnU701_y8M_zxbStsi9gKBtzqHI9lC2VCKXTr_gdW-a-wUubNy2HNOO36VV6Aw'; 
    
    //     $data = [
    //         'status' => 'en attente',
    //         'typeDon' => 'Nourriture',
    //         'adresseProvenance' => 'Mariste',
    //         'descriptionDon' => 'pour la nouriture des talibe',
    //         'disponibiliteDon' => 'immediate',
    //         'dahra_id' => 1,
    //         'user_id' => 2,
    //     ];
    
    //     // Ajoutez l'en-tête d'autorisation à la demande
    //     $client->request(
    //         'POST',
    //         '/api/faire-donTest',
    //         [],
    //         [],
    //         ['CONTENT_TYPE' => 'application/json', 'HTTP_Authorization' => 'Bearer ' . $token],
    //         json_encode($data)
    //     );
    
    //     $this->assertResponseIsSuccessful();
    //     $this->assertEquals(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());
    //     $this->assertJson($client->getResponse()->getContent());
    //     $responseData = json_decode($client->getResponse()->getContent(), true);
    //     $this->assertEquals('Don effectué avec succès', $responseData['message']);
    // }

    public function testFaireDonSansToken()
{
    $client = static::createClient();

    $data = [
        'status' => 'en attente',
        'typeDon' => 'Nourriture',
        'adresseProvenance' => 'Mariste',
        'descriptionDon' => 'pour la nouriture des talibe',
        'disponibiliteDon' => 'immediate',
        'dahra_id' => 1,
        'user_id' => 1,
    ];

  
    $client->request(
        'POST',
        '/api/faire-donTest',
        [],
        [],
        ['CONTENT_TYPE' => 'application/json'],
        json_encode($data)
    );

    $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
}
}
