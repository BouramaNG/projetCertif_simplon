<?php
// src/Security/AuthenticationSuccessHandler.php

namespace App\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\HttpFoundation\Request;

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

        return new JsonResponse([
            'token' => $jwtToken,
            'user' => [
                // 'id' => $user->getId(),
                // 'nom' => $user->getNom(),
                // 'prenom' => $user->getPrenom(),
                'email' => $user->getEmail(),
                // 'adresse' => $user->getAdresse(),
                'roles' => $user->getRoles(),
                
            ]
        ]);
    }
}
