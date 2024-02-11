<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Talibe;
use App\Entity\Parrainage;
use OpenApi\Annotations as OA;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

 /**
 * @OA\Post(
 *     path="/api/creer-parrainage",
 *     summary="Créer un parrainage",
 *     description="Permet à un utilisateur avec le rôle de marraine de créer un parrainage pour un talibé.",
 *     @OA\RequestBody(
 *         required=true,
 *         description="Données nécessaires pour créer un parrainage",
 *         @OA\JsonContent(
 *             required={"talibe_id", "typeParrainage"},
 *             @OA\Property(property="talibe_id", type="integer", example=1),
 *             @OA\Property(property="typeParrainage", type="string", example="ndeyeDahra")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Parrainage créé avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Parrainage créé avec succès"),
 *             @OA\Property(property="nom_du_dahra", type="string", example="Nom du Dahra associé au Talibe")
 *         )
 *     ),
 *     @OA\Response(response=400, description="Données invalides fournies"),
 *     @OA\Response(response=403, description="Accès refusé"),
 *     @OA\Response(response=404, description="Talibe introuvable"),
 *     security={{"bearerAuth": {}}}
 * )
 */
#[Route('/api', name: 'api_')]
class ParrainageController extends AbstractController
{
    #[Route('/creer-parrainage', name: 'creer_parrainage', methods: ['POST'])]
public function creerParrainage(Request $request, EntityManagerInterface $em, SerializerInterface $serializer, ValidatorInterface $validator, Security $security): JsonResponse
{
   

    $data = json_decode($request->getContent(), true);
    
    $user = $security->getUser();
    if (!$user || !in_array('ROLE_MARRAINE', $user->getRoles())) {
        return new JsonResponse(['message' => 'Accès refusé'], JsonResponse::HTTP_FORBIDDEN);
    }
    

    $talibe = $em->getRepository(Talibe::class)->find($data['talibe_id']);
    if (!$talibe) {
        return new JsonResponse(['message' => 'Talibe introuvable'], JsonResponse::HTTP_NOT_FOUND);
    }

    // Récupérer le Dahra associé au Talibe
    $dahraDuTalibe = $talibe->getDahra();
    if (!$dahraDuTalibe) {
        return new JsonResponse(['message' => 'Dahra non associé à ce Talibe'], JsonResponse::HTTP_BAD_REQUEST);
    }
    $nomDuDahra = $dahraDuTalibe->getNom();

    $parrainageExistant = $em->getRepository(Parrainage::class)->findOneBy([
        'user' => $user,
        'talibe' => $talibe
    ]);

    if ($parrainageExistant) {
        return new JsonResponse(['message' => 'Ce Talibe est déjà parrainé par vous'], JsonResponse::HTTP_BAD_REQUEST);
    }


   
    $typeParrainage = $data['typeParrainage'] ?? 'ndeyeDahra'; 
    $status = 'en cours'; 

    
    $parrainage = new Parrainage();
    $parrainage->setUser($user);
    $parrainage->setTalibe($talibe);
    $parrainage->setTypeParrainage($typeParrainage);
    $parrainage->setStatus($status);
    $parrainage->setDate(new \DateTime());

    
    $errors = $validator->validate($parrainage);
    if (count($errors) > 0) {
        return new JsonResponse((string) $errors, JsonResponse::HTTP_BAD_REQUEST);
    }


    $em->persist($parrainage);
    $em->flush();

    return new JsonResponse(['message' => 'Parrainage créé avec succès', 'nom_du_dahra' => $nomDuDahra], JsonResponse::HTTP_CREATED);
}




/**
 * Ajouter un nouveau Talibe à un Dahra.
 * 
 * @OA\Post(
 *     path="/api/dahra/add-talibe",
 *     summary="Ajouter un Talibe à un Dahra Par un dahra",
 *     description="Permet à un utilisateur avec le rôle Dahra d'ajouter un nouveau Talibe au Dahra.",
 *     @OA\RequestBody(
 *         required=true,
 *         description="Informations nécessaires pour l'ajout d'un Talibe",
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"nom", "prenom", "age", "adresse", "situation", "description", "datearrivetalibe", "imageFile"},
 *                 @OA\Property(property="nom", type="string", example="Amadou"),
 *                 @OA\Property(property="prenom", type="string", example="Diop"),
 *                 @OA\Property(property="age", type="integer", example=12),
 *                 @OA\Property(property="adresse", type="string", example="123 Rue de Dakar"),
 *                 @OA\Property(property="situation", type="string", example="orphelin"),
 *                 @OA\Property(property="description", type="string", example="Enfant calme et studieux"),
 *                 @OA\Property(property="datearrivetalibe", type="string", format="date", example="2024-01-01"),
 *                 @OA\Property(property="imageFile", type="string", format="binary", description="Fichier image à uploader")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Talibe ajouté avec succès",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Choukrane vous avez ajouté avec succès un Talibe !"),
 *             @OA\Property(property="talibeId", type="integer", example=1)
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Accès refusé"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Dahra non trouvé"
 *     )
 * )
 */


}
