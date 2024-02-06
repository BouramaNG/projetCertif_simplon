<?php

namespace App\Tests\Functional;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FaireDonTest extends WebTestCase
{
    public function testFaireDon()
    {
        $client = static::createClient();
    
        // Générez un jeton JWT fictif
        $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE3MDY5NjQ0MDIsImV4cCI6MTcwNjk2ODAwMiwicm9sZXMiOlsiUk9MRV9EQUhSQSIsIlJPTEVfVVNFUiJdLCJ1c2VybmFtZSI6Im5nb21ib3VyYW1hNjVAZ21haWwuY29tIn0.RDhub9cWTEaQV2zNS_AP50icwWWyCjPPYYG88dRwf2P43iHkH8qT0mtanrs6WEl3s6KbGh5jp_V-9582pXm998OMC5FEHm8p_f4LpFNeyeFgXOluOXwmeNqwHtN9fzdoDSzDoA0Yyc7n-rDtkLHEKaWsyDyQvHQFB1dsIWoOa-pT_jLchdx3XuzwEmQWgnJjRN65zv4xat0Cq1YPJ8uTiWTITt8PjjyClA-_XKbY0forFEnEW6ElR4llUto0Q7gEiQms3s8A_1E2JAaRUC1kHkvQQ6Xk5tcxtvm4z-jTVulsujefAoUMlKYE3VN7OodAwirxmgWryvqDDqd5n2D9TiDi87nqCJS3BwHwpp3C29uxN-1YCUEv-_bt1YZ0rq30cdg5xVzdZy0fp8oSLxRM0LDoAMFlgdpDG5ZHRdb3C30nZmAHnSsa6B5qkyLym_-DsyhHANQLTQJw1O5PEs-4BQocwVnrzmOSRRiClobqde0MdYgWwm3H0paYIk4x1B8jBbYhYVem0xV-a7FX-L4iU58h2tNdr-MSYlBNg0zVWYx_ebreLW82qXTVPWGwCCXV88PPSZAV_BZnATYQvd0OkJj0Ta0DEvAFUygLWghVmSqssXUZvWu31qiHYdIDDBIKxvcgDWDJsxeaw7GtFdYRUItgrnRUTFIzYeIkmTbpShQ'; // Remplacez cela par le jeton valide pour votre application
    
        $data = [
            'status' => 'en attente',
            'typeDon' => 'Nourriture',
            'adresseProvenance' => 'Mariste',
            'descriptionDon' => 'pour la nouriture des talibe',
            'disponibiliteDon' => 'immediate',
            'dahra_id' => 1,
            'user_id' => 2,
        ];
    
        // Ajoutez l'en-tête d'autorisation à la demande
        $client->request(
            'POST',
            '/api/faire-donTest',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_Authorization' => 'Bearer ' . $token],
            json_encode($data)
        );
    
        $this->assertResponseIsSuccessful();
        $this->assertEquals(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals('Don effectué avec succès', $responseData['message']);
    }
}
