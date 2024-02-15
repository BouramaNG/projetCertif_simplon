<?php

namespace App\Controller;

use App\Service\TokenBlacklistService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use OpenApi\Annotations as OA;

#[Route('/api', name: 'api_')]
class LogoutController extends AbstractController
{
    private $tokenBlacklistService;

    public function __construct(TokenBlacklistService $tokenBlacklistService)
    {
        $this->tokenBlacklistService = $tokenBlacklistService;
    }

/** 
*
* 
*
* @OA\Post(
*path="/api/logout",
*summary="Logout user and invalidate token",
*     tags={"Authentication"},
*     @OA\RequestBody(
*         @OA\MediaType(
*             mediaType="application/json",
*             @OA\Schema(
*                 @OA\Property(property="Authorization", type="string", example="Bearer YOUR_ACCESS_TOKEN"),
*             ),
*         ),
*     ),
*     @OA\Response(
*         response="200",
*         description="Successfully logged out",
*         @OA\JsonContent(
*             @OA\Property(property="message", type="string", example="Déconnexion réussie"),
*         ),
*     ),
*     @OA\Response(
*         response="400",
*         description="Token not provided",
*         @OA\JsonContent(
*             @OA\Property(property="message", type="string", example="Token non fourni"),
*         ),
*     ),
* )
*/

#[Route('/logout', name: 'api_logout', methods: ['POST'])]
    public function logout(Request $request, Security $security): JsonResponse
    {
        $authorizationHeader = $request->headers->get('Authorization');
    
        if (!$authorizationHeader) {
            return new JsonResponse(['message' => 'Token non fourni'], JsonResponse::HTTP_BAD_REQUEST);
        }
    
        $token = str_replace('Bearer ', '', $authorizationHeader);
    
        
        $this->tokenBlacklistService->addToBlacklist($token);
    
    
    
        return new JsonResponse(['message' => 'Déconnexion réussie']);
    }
    
}
