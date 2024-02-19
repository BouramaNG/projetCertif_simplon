<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Newsletter;
use OpenApi\Annotations as OA;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
     *     )
     * )
     */
    #[Route("/newsletter/souscription", name:"api_newsletter_souscription", methods:["POST"])]
    public function subscribe(Request $request, EntityManagerInterface $em, MailerInterface $mailer): Response
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return new JsonResponse(['message' => 'Email invalide'], Response::HTTP_BAD_REQUEST);
        }
    
        $newsletter = new Newsletter();
        $newsletter->setEmail($email);
        $newsletter->setCreatedAt(new \DateTime());
    
        $em->persist($newsletter);
        $em->flush();
    
        $email = (new Email())
            ->from('ngombourama@gmail.com')
            ->to($email)
            ->subject('Confirmation d\'abonnement à la Newsletter')
            ->html('<p>Merci pour votre abonnement à notre Newsletter</p>');
    
        $mailer->send($email);
    
        return new Response('Merci pour votre abonnement à notre Newsletter', Response::HTTP_CREATED);
    }
    
}
