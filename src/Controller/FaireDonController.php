<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Dahra;
use App\Entity\FaireDon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use OpenApi\Annotations as OA;
  /**
 * @OA\Post(
 *     path="/api/faire-don",
 *     summary="Effectuer un don",
 *     description="Permet à un utilisateur connecté d'effectuer un don à un Dahra spécifique.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"dahra_name"},
 *             @OA\Property(property="status", type="string", example="en attente", description="Statut du don"),
 *             @OA\Property(property="typeDon", type="string", example="Vêtements", description="Type du don"),
 *             @OA\Property(property="adresseProvenance", type="string", example="123 Rue Exemple, Ville", description="Adresse de provenance du don"),
 *             @OA\Property(property="descriptionDon", type="string", example="Description détaillée du don"),
 *             @OA\Property(property="disponibiliteDon", type="string", example="Disponible immédiatement", description="Disponibilité du don"),
 *             @OA\Property(property="dahra_name", type="string", example="Nom Dahra", description="Nom du Dahra bénéficiaire du don")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Don effectué avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Don effectué avec succès")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Données invalides ou requête mal formulée"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Dahra non trouvé"
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Utilisateur non connecté"
 *     )
 *    
 * )
 */
#[Route('/api', name: 'api_')]
class FaireDonController extends AbstractController
{

  
    #[Route('/faire-don', name: 'faire_don', methods: ['POST'])]
    public function faireDon(Request $request, EntityManagerInterface $em, ValidatorInterface $validator, Security $security, MailerInterface $mailer): JsonResponse
{
    $data = json_decode($request->getContent(), true);
    $user = $security->getUser();
    if (!$user || !in_array('ROLE_DONATEUR', $user->getRoles())) {
        return new JsonResponse(['message' => 'Accès refusé'], JsonResponse::HTTP_FORBIDDEN);
    }

    $faireDon = new FaireDon();
    $faireDon->setDate(new \DateTime());
    $faireDon->setStatus($data['status'] ?? 'en attente');
    $faireDon->setTypeDon($data['typeDon'] ?? null);
    $faireDon->setAdresseProvenance($data['adresseProvenance'] ?? null);
    $faireDon->setDescriptionDon($data['descriptionDon'] ?? null);
    $faireDon->setDisponibiliteDon($data['disponibiliteDon'] ?? null);
    $dahraName = $data['dahra_name'] ?? null;
    $dahra = $em->getRepository(Dahra::class)->findOneBy(['nom' => $dahraName]);

    if (!$dahra) {
        return new JsonResponse(['message' => 'Dahra non trouvé'], JsonResponse::HTTP_NOT_FOUND);
    }

    $faireDon->setUser($user);
    $faireDon->setDahra($dahra);

    $errors = $validator->validate($faireDon);
    if (count($errors) > 0) {
        return new JsonResponse((string) $errors, JsonResponse::HTTP_BAD_REQUEST);
    }

    $em->persist($faireDon);
    $em->flush();

    $marraine = $faireDon->getUser();

    if ($marraine && $marraine->getEmail()) {
        $email = (new Email())
            ->from('ngombourama@gmail.com')
            ->to($marraine->getEmail())
            ->subject('Confirmation de don')
            ->html('<p>Merci ! pour votre don. partager avec le peu que vous avez pour les personnes dans le besoins est signes de bonnes foi </p>');
    
        $mailer->send($email);
    }

    return new JsonResponse(['message' => 'Don effectué avec succès'], JsonResponse::HTTP_CREATED);
}

/**
 * @OA\Get(
 *     path="/api/liste-dons",
 *     summary="Liste des dons",
 *     description="Récupère la liste de tous les dons effectués.",
 *     @OA\Response(
 *         response=200,
 *         description="Liste des dons récupérée avec succès",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="date", type="string", format="datetime", example="2024-01-20 15:00:00"),
 *                 @OA\Property(property="status", type="string", example="en attente"),
 *                 @OA\Property(property="typeDon", type="string", example="Vêtements"),
 *                 @OA\Property(property="adresseProvenance", type="string", example="123 Rue Exemple, Ville"),
 *                 @OA\Property(property="descriptionDon", type="string", example="Description détaillée du don"),
 *                 @OA\Property(property="disponibiliteDon", type="string", example="Disponible immédiatement"),
 *                 @OA\Property(
 *                     property="user",
 *                     type="object",
 *                     @OA\Property(property="id", type="integer", example=2),
 *                     @OA\Property(property="nom", type="string", example="Dupont"),
 *                     @OA\Property(property="prenom", type="string", example="Jean"),
 *                     @OA\Property(property="numeroTelephone", type="string", example="+33123456789")
 *                 ),
 *                 @OA\Property(
 *                     property="dahra",
 *                     type="object",
 *                     @OA\Property(property="id", type="integer", example=3),
 *                     @OA\Property(property="nom", type="string", example="Dahra Al Azhar"),
 *                     @OA\Property(property="nomOuztas", type="string", example="Ouztas Ahmed"),
 *                     @OA\Property(property="numeroTelephoneOuztas", type="string", example="+33198765432")
 *                 )
 *             )
 *         )
 *     )
 * )
 */ 

    #[Route('/liste-dons', name: 'liste_dons', methods: ['GET'])]
public function listeDons(EntityManagerInterface $em): JsonResponse
{
    $dons = $em->getRepository(FaireDon::class)->findAll();

    $donsArray = [];

    foreach ($dons as $don) {
        $donsArray[] = [
            'id' => $don->getId(),
            'date' => $don->getDate()->format('Y-m-d H:i:s'),
            'status' => $don->getStatus(),
            'typeDon' => $don->getTypeDon(),
            'adresseProvenance' => $don->getAdresseProvenance(),
            'descriptionDon' => $don->getDescriptionDon(),
            'disponibiliteDon' => $don->getDisponibiliteDon(),
            'user' => [
                'id' => $don->getUser()->getId(),
                'nom' => $don->getUser()->getNom(),
                'prenom' => $don->getUser()->getPrenom(),
                'numeroTelephone' => $don->getUser()->getNumeroTelephone(),
               
            ],
            'dahra' => [
                'id' => $don->getDahra()->getId(),
                'nom' => $don->getDahra()->getNom(),
                'nomOuztas' => $don->getDahra()->getNomOuztas(),
                'numeroTelephoneOuztas' => $don->getDahra()->getNumeroTelephoneOuztas(),
               
            ],
        ];
    }

    return new JsonResponse($donsArray, JsonResponse::HTTP_OK);
}

#[Route('/faire-donTest', name: 'faire_donTest', methods: ['POST'])]
    public function faireDonTest(Request $request, EntityManagerInterface $em, ValidatorInterface $validator, Security $security): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
    
        $faireDon = new FaireDon();
        $faireDon->setDate(new \DateTime());
        $faireDon->setStatus($data['status'] ?? 'en attente');
        $faireDon->setTypeDon($data['typeDon'] ?? null);
        $faireDon->setAdresseProvenance($data['adresseProvenance'] ?? null);
        $faireDon->setDescriptionDon($data['descriptionDon'] ?? null);
        $faireDon->setDisponibiliteDon($data['disponibiliteDon'] ?? null);
        $dahraName = $data['dahra_name'] ?? null;
        $dahra = $em->getRepository(Dahra::class)->findOneBy(['nom' => $dahraName]);
    
    
        $em->persist($faireDon);
        $em->flush();
    
        return new JsonResponse(['message' => 'Don effectué avec succès'], JsonResponse::HTTP_CREATED);
    }

/**
 * @OA\Get(
 *     path="/api/dons_donateur",
 *     summary="Liste des dons d'un donateur",
 *     description="Récupère la liste de tous les dons effectués par un donateur.",
 *     @OA\Response(
 *         response=200,
 *         description="Liste des dons récupérée avec succès",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 type="object",
 *                 @OA\Property(property="date", type="string", format="datetime", example="2024-01-20 15:00:00"),
 *                 @OA\Property(property="status", type="string", example="en attente"),
 *                 @OA\Property(property="adresse_provenance", type="string", example="123 Rue Exemple, Ville"),
 *                 @OA\Property(property="description_don", type="string", example="Description détaillée du don"),
 *                 @OA\Property(property="dahra_name", type="string", example="Dahra Al Azhar"),
 *                 @OA\Property(property="dahra_adresse", type="string", example="Adresse de la Dahra"),
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Accès refusé",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Accès refusé")
 *         )
 *     )
 * )
 */
    #[Route('/dons_donateur', name: 'dons_donateur', methods: ['GET'])]
public function DonDonateur(EntityManagerInterface $em, Security $security): JsonResponse
{
    $user = $security->getUser();
    if (!$user || !in_array('ROLE_DONATEUR', $user->getRoles())) {
        return new JsonResponse(['message' => 'Accès refusé'], JsonResponse::HTTP_FORBIDDEN);
    }

    $dons = $em->getRepository(FaireDon::class)->findBy(['User' => $user]);

    $results = [];
    foreach ($dons as $don) {
        $dahra = $don->getDahra();

        $results[] = [
            'date' => $don->getDate()->format('Y-m-d H:i:s'),
            'status' => $don->getStatus(),
            'adresse_provenance' => $don->getAdresseProvenance(),
            'description_don' => $don->getDescriptionDon(),
            'dahra_name' => $dahra->getNom(),
            'dahra_adresse' => $dahra->getAdresse(),
           
        ];
    }

    return new JsonResponse(['dons' => $results], JsonResponse::HTTP_OK);
}
    
}
