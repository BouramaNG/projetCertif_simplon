<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
#[Route('/api', name: 'api_')]
class SecurityController extends AbstractController
{
    
    #[Route('/login', name: 'api_login',methods: ['POST'])]
   
    public function login(): JsonResponse
    {
        
        throw new \Exception('Should not be reached');
    }

  


}


