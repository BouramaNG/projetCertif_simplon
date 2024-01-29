<?php

namespace App\Controller;

use App\Entity\Newsletter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use OpenApi\Annotations as OA;

#[Route('/api', name: 'api_')]
class NewsletterController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @Route("/newsletter/souscription", name="api_newsletter_souscription", methods={"POST"})
     *
     * @OA\Post(
     *     path="/api/newsletter/souscription",
     *     summary="Subscribe to the newsletter",
     *     tags={"Newsletter"},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="email", type="string", example="user@example.com"),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Successfully subscribed to the newsletter",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Merci ! pour votre abonnement à notre Newsletter"),
     *         ),
     *     ),
     * )
     */
    #[Route("/newsletter/souscription", name:"api_newsletter_souscription", methods:["POST"])]
    public function subscribe(Request $request, EntityManagerInterface $em): Response
    {
        $data = json_decode($request->getContent(), true);
    
        $newsletter = new Newsletter();
        $newsletter->setEmail($data['email']);
        $newsletter->setCreatedAt(new \DateTime());
    
        $user = $this->security->getUser();
        if ($user) {
            $newsletter->setUser($user);
        }
    
        $em->persist($newsletter);
        $em->flush();
    
        return new Response('Merci ! pour votre abonnement a notre Newsletter', Response::HTTP_CREATED);
    }
    
}
