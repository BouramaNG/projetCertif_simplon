<?php 
namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiIpService
{
    private $apiKey;

    public function __construct(string $apiKey)
    {
        // $this->apiKey = $apiKey;
    }
    public function setApiKey(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }


    public function getIpAddressDetails(string $ipAddress)
    {
        $httpClient = HttpClient::create([
            'verify_peer' => false,
            'verify_host' => false,
        ]);

        $response = $httpClient->request('GET', 'https://apiip.net/' . $ipAddress . '?api-key=' . $this->apiKey);
        $data = $response->toArray();

        return $data;
    }
}