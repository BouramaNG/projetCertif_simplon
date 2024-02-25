<?php
// src/Security/AuthenticationSuccessHandler.php

namespace App\Security;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class AuthenticationSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    private $jwtTokenManager;

    public function __construct(JWTTokenManagerInterface $jwtTokenManager)
    {
        $this->jwtTokenManager = $jwtTokenManager;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): JsonResponse
    {
        $user = $token->getUser();
        $jwtToken = $this->jwtTokenManager->create($user);

       
    // $ipAddress = $request->getClientIp();

   
    $client = HttpClient::create();
    $response = $client->request('GET', 'https://api.ipgeolocation.io/ipgeo?apiKey=14f46a4dfa064abdab4b0b6a995921d2');
    $data = $response->toArray();
    $ipAddress = $data['ip'];
    $latitude = $data['latitude'];
    $longitude = $data['longitude'];

    // Récupérer l'appareil de l'utilisateur à partir de l'en-tête User-Agent
    $userAgent = $request->headers->get('User-Agent');
    $roles = $user->getRoles();
    $filteredRoles = array_filter($roles, function ($role) {
        return $role === 'ROLE_DAHRA' || $role === 'ROLE_ADMIN' || $role === 'ROLE_DONATEUR' || $role === 'ROLE_MARRAINE';
    });
        return new JsonResponse([
            'token' => $jwtToken,
            'user' => [
                // 'id' => $user->getId(),
                // 'nom' => $user->getNom(),
                // 'prenom' => $user->getPrenom(),
                'email' => $user->getEmail(),
                // 'adresse' => $user->getAdresse(),
                'roles' => $filteredRoles,
                
            ],
            'ip_address' => $ipAddress,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'user_agent' => $userAgent,
        ]);
    }
}
