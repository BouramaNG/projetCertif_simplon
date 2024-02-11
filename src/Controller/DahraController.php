<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Dahra;
use App\Entity\Talibe;
use App\Entity\FaireDon;
use App\Service\FileUploader;
use OpenApi\Annotations as OA;
use App\Repository\DahraRepository;
use App\Repository\TalibeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


/**
 * @OA\Post(
 *     path="/api/inscription/dahra",
 *     summary="Enregistrer un nouveau Dahra",
 *     description="Permet d'enregistrer un nouveau Dahra avec les informations fournies.",
 *     @OA\RequestBody(
 *         required=true,
 *         description="Données nécessaires pour l'enregistrement d'un Dahra",
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"email", "password", "nom", "nomOuztas", "adresse", "region", "numeroTelephoneOuztas","numeroTelephone", "nombreTalibe", "imageFile"},
 *                 @OA\Property(property="email", type="string", format="email", example="contact@gmail.com"),
 *                 @OA\Property(property="numeroTelephone", type="string", example="783718472"),
 *                 @OA\Property(property="password", type="string", format="password", example="Passer123"),
 *                 @OA\Property(property="nom", type="string", example="Dahra Al Azhar"),
 *                 @OA\Property(property="nomOuztas", type="string", example="Cheikh Modou"),
 *                 @OA\Property(property="adresse", type="string", example="Marabouts"),
 *                 @OA\Property(property="region", type="string", example="Dakar"),
 *                 @OA\Property(property="numeroTelephoneOuztas", type="string", example="783718472"),
 *                 @OA\Property(property="nombreTalibe", type="integer", example=100),
 *                 @OA\Property(property="imageFile", type="string", format="binary", description="Fichier image du Dahra")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Dahra enregistré avec succès",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="nom", type="string", example="Dahra Al Azhar"),
 *             @OA\Property(property="region", type="string", example="Dakar"),
 *            
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Données invalides fournies"
 *     ),
 *     security={
 *         {"Bearer": {}}
 *     }
 * )
 * @IgnoreAnnotation("Security")
 */

#[Route('/api', name: 'api_')]
class DahraController extends AbstractController
{
 
#[Route('/inscription/dahra', name: 'api_dahra_register', methods: ['POST'])]
 
    public function registerDahra(Request $request, UserPasswordHasherInterface $passwordHasher, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator, FileUploader $fileUploader): JsonResponse
{
    
    $data = json_decode($request->getContent(), true);
   
    
        $constraints = new Assert\Collection([
            'email' => [new Assert\NotBlank(), new Assert\Email()],
            'password' => [new Assert\NotBlank()],
            'nom' => [new Assert\NotBlank(), new Assert\Type(['type' => 'string'])],
            'nomOuztas' => [new Assert\NotBlank(), new Assert\Type(['type' => 'string'])],
            'adresse' => [new Assert\NotBlank(), new Assert\Type(['type' => 'string'])],
            'region' => [new Assert\NotBlank(), new Assert\Type(['type' => 'string'])],
            'numeroTelephone' => [
                new Assert\NotBlank(),
                new Assert\Regex(['pattern' => '/^(77|78|76|70)\d{7}$/']),
            ],
            'numeroTelephoneOuztas' => [
                new Assert\NotBlank(),
                new Assert\Regex(['pattern' => '/^(77|78|76|70)\d{7}$/']),
            ],
            'nombreTalibe' => [new Assert\NotBlank(), new Assert\Type(['type' => 'integer'])],
        ]);
    
        $violations = $validator->validate($data, $constraints);
    
        if (count($violations) > 0) {
            return new JsonResponse(['errors' => (string) $violations], JsonResponse::HTTP_BAD_REQUEST);
        }
    

    $user = new User();
    $user->setEmail($request->request->get('email'));
    $user->setNumeroTelephone($request->request->get('numeroTelephone'));
    $user->setPassword($passwordHasher->hashPassword($user, $request->request->get('password')));
    $user->setRoles(['ROLE_DAHRA']);
    $user->setIsActive(false);

    $dahra = new Dahra();
    $dahra->setNom($request->request->get('nom'));
    $dahra->setNomOuztas($request->request->get('nomOuztas'));
    $dahra->setAdresse($request->request->get('adresse'));
    $dahra->setRegion($request->request->get('region'));
    $dahra->setNumeroTelephoneOuztas($request->request->get('numeroTelephoneOuztas'));
    $dahra->setNombreTalibe($request->request->get('nombreTalibe'));
    $dahra->setUser($user);
       
    if ($request->files->has('imageFile')) {
        $imageFile = $request->files->get('imageFile');
        if ($imageFile) {
          
            $uploadedFilePath = $fileUploader->upload($imageFile);
            
        
            $dahra->setImageFilename($uploadedFilePath);
        }
    }

    $entityManager->persist($user);
    $entityManager->persist($dahra);
    $entityManager->flush();

    $responseData = $serializer->serialize($dahra, 'json');
    return new JsonResponse($responseData, Response::HTTP_CREATED, [], true);
}
/**
 * Modifier les informations d'un Dahra.
 * 
 * @OA\Put(
 *     path="/api/modifier-dahra/{id}",
 *     summary="Modifier les informations d'un Dahra",
 *     description="Permet à un Dahra de mettre à jour ses informations en spécifiant l'identifiant du Dahra dans l'URL.",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="Identifiant du Dahra à modifier",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         description="Données nécessaires pour la mise à jour du Dahra",
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 @OA\Property(property="nom", type="string", example="Nouveau Nom"),
 *                 @OA\Property(property="nomOuztas", type="string", example="Nouveau Nom Ouztas"),
 *                 @OA\Property(property="adresse", type="string", example="Nouvelle Adresse"),
 *                 @OA\Property(property="region", type="string", example="Nouvelle Région"),
 *                 @OA\Property(property="numeroTelephoneOuztas", type="string", example="Nouveau Numéro de Téléphone Ouztas"),
 *                 @OA\Property(property="nombreTalibe", type="integer", example=50),
 *                 @OA\Property(property="password", type="string", example="NouveauMotDePasse"),
 *                 @OA\Property(property="imageFile", type="file", format="binary"),
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Informations du Dahra mises à jour avec succès",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="nom", type="string", example="Nouveau Nom"),
 *             @OA\Property(property="nomOuztas", type="string", example="Nouveau Nom Ouztas"),
 *             @OA\Property(property="adresse", type="string", example="Nouvelle Adresse"),
 *             @OA\Property(property="region", type="string", example="Nouvelle Région"),
 *             @OA\Property(property="numeroTelephoneOuztas", type="string", example="Nouveau Numéro de Téléphone Ouztas"),
 *             @OA\Property(property="nombreTalibe", type="integer", example=50),
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Dahra non trouvé"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Erreur de validation ou problème lors de la mise à jour"
 *     ),
 *     security={
 *         {"bearerAuth": {}}
 *     }
 * )
 */

    
 #[Route('/modifier-dahra/{id}', name: 'api_dahra_modifier', methods: ['PUT'])]
 public function updateDahra(int $id, Request $request,ValidatorInterface $validator, UserPasswordHasherInterface $passwordHasher, SerializerInterface $serializer, EntityManagerInterface $entityManager,FileUploader $fileUploader): JsonResponse
 {
     $data = json_decode($request->getContent(), true);
     $constraints = new Assert\Collection([
        'email' => [new Assert\NotBlank(), new Assert\Email()],
        'password' => [new Assert\NotBlank()],
        'nom' => [new Assert\NotBlank(), new Assert\Type(['type' => 'string'])],
        'nomOuztas' => [new Assert\NotBlank(), new Assert\Type(['type' => 'string'])],
        'adresse' => [new Assert\NotBlank(), new Assert\Type(['type' => 'string'])],
        'region' => [new Assert\NotBlank(), new Assert\Type(['type' => 'string'])],
        'numeroTelephone' => [
            new Assert\NotBlank(),
            new Assert\Regex(['pattern' => '/^(77|78|76|70)\d{7}$/']),
        ],
        'numeroTelephoneOuztas' => [
            new Assert\NotBlank(),
            new Assert\Regex(['pattern' => '/^(77|78|76|70)\d{7}$/']),
        ],
        'nombreTalibe' => [new Assert\NotBlank(), new Assert\Type(['type' => 'integer'])],
    ]);

    $violations = $validator->validate($data, $constraints);

    if (count($violations) > 0) {
        return new JsonResponse(['errors' => (string) $violations], JsonResponse::HTTP_BAD_REQUEST);
    }
 
     $dahra = $entityManager->getRepository(Dahra::class)->find($id);
 
     if (!$dahra) {
         return new JsonResponse(['error' => 'Dahra not found'], Response::HTTP_NOT_FOUND);
     }
     $requestData = json_decode($request->getContent(), true);

    $dahra->setNom($requestData['nom'] ?? $dahra->getNom());
    $dahra->setNomOuztas($requestData['nomOuztas'] ?? $dahra->getNomOuztas());
    $dahra->setAdresse($requestData['adresse'] ?? $dahra->getAdresse());
    $dahra->setRegion($requestData['region'] ?? $dahra->getRegion());
    $dahra->setNumeroTelephoneOuztas($requestData['numeroTelephoneOuztas'] ?? $dahra->getNumeroTelephoneOuztas());
    $dahra->setNombreTalibe($requestData['nombreTalibe'] ?? $dahra->getNombreTalibe());

    if ($request->files->has('imageFile')) {
        $imageFile = $request->files->get('imageFile');
        if ($imageFile) {
            $uploadedFilePath = $fileUploader->upload($imageFile);
            $dahra->setImageFilename($uploadedFilePath);
        }
    }
    if (!empty($requestData['password'])) {
        $user = $dahra->getUser();
        $user->setPassword($passwordHasher->hashPassword($user, $requestData['password']));
    }

    $entityManager->flush();
    return new JsonResponse(['message' => 'Les informations du Dahra ont été mises à jour avec succès'], Response::HTTP_OK);
 }




/**
 * Modifier les informations d'un Talibe.
 * 
 * @OA\Put(
 *     path="/api/modifier_info_talibe/{id}",
 *     summary="Modifier les informations d'un Talibe",
 *     description="Permet à un utilisateur avec le rôle Dahra de modifier les informations d'un Talibe dans le Dahra.",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID du Talibe à modifier",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         description="Informations à modifier pour le Talibe Par son dahra",
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"nom", "prenom", "age", "adresse", "situation", "description", "datearrivetalibe"},
 *                 @OA\Property(property="nom", type="string", example="Amadou"),
 *                 @OA\Property(property="prenom", type="string", example="Diop"),
 *                 @OA\Property(property="age", type="integer", example=12),
 *                 @OA\Property(property="adresse", type="string", example="123 Rue de Dakar"),
 *                 @OA\Property(property="situation", type="string", example="orphelin"),
 *                 @OA\Property(property="description", type="string", example="Enfant calme et studieux"),
 *                 @OA\Property(property="datearrivetalibe", type="string", format="date", example="2024-01-01"),
 *                 @OA\Property(property="imageFile", type="string", format="binary", description="Fichier image (optionnel)")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Talibe modifié avec succès",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Les informations du Talibe ont été modifiées avec succès")
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Accès refusé"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Talibe non trouvé"
 *     )
 * )
 */


    #[Route('/modifier_info_talibe/{id}', name: 'modifier', methods: ['GET', 'PUT'])]
    public function modifierTalibe(int $id,Security $security, Request $request, EntityManagerInterface $entityManager,FileUploader $fileUploader,ValidatorInterface $validator): JsonResponse
    {

        $user = $security->getUser();
        if (!$user || !in_array('ROLE_DAHRA', $user->getRoles())) {
            return new JsonResponse(['message' => 'Accès refusé'], JsonResponse::HTTP_FORBIDDEN);
        }

        $talibe = $entityManager->getRepository(Talibe::class)->find($id);

        if (!$talibe) {
            return new JsonResponse(['message' => 'Talibe non trouvé'], JsonResponse::HTTP_NOT_FOUND);
        }

        if ($request->isMethod('GET')) {
            return $this->json([
                'id' => $talibe->getId(),
                'nom' => $talibe->getNom(),
                'prenom' => $talibe->getPrenom(),
                'age' => $talibe->getAge(),
                'adresse' => $talibe->getAdresse(),
                'situation' => $talibe->getSituation(),
                'description' => $talibe->getDescription(),
            ], JsonResponse::HTTP_OK);
        }

        $talibe->setNom($request->request->get('nom'));
        $talibe->setPrenom($request->request->get('prenom'));
        $talibe->setAge($request->request->get('age'));
        $talibe->setAdresse($request->request->get('adresse'));
        $talibe->setSituation($request->request->get('situation'));
        $talibe->setDescription($request->request->get('description'));
        $talibe->setDateArriveTalibe($request->request->get('datearrivetalibe'));
        $talibe->setPresenceTalibe($request->request->get('presencetalibe') ?? 'present');

        if ($request->files->has('imageFile')) {
            $imageFile = $request->files->get('imageFile');
            if ($imageFile) {
                $uploadedFilePath = $fileUploader->upload($imageFile);
                $talibe->setImageFilename($uploadedFilePath);
            }
        }

        $violations = $validator->validate($talibe);
        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[$violation->getPropertyPath()] = $violation->getMessage();
            }
            return $this->json(['errors' => $errors], JsonResponse::HTTP_BAD_REQUEST);
        }

        $entityManager->flush();

        return $this->json(['message' => 'Les informations du Talibe ont été modifiées avec succès'], JsonResponse::HTTP_OK);
    }
    
/**
 * @OA\Post(
 *     path="/api/dahra/modifier-presence-talibe/{talibeId}",
 *     summary="Modifier la présence d'un Talibe",
 *     description="Permet à un utilisateur avec le rôle Dahra de mettre à jour la présence d'un Talibe spécifique.",
 *     @OA\Parameter(
 *         name="talibeId",
 *         in="path",
 *         required=true,
 *         description="ID du Talibe à mettre à jour",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Présence du Talibe mise à jour avec succès",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Choukrane cela signifie que le Talibe n'est plus dans le dahra !")
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Accès refusé"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Talibe non trouvé"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Requête invalide",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Erreur de requête")
 *         )
 *     )
 * )
 */
    #[Route('/dahra/modifier-presence-talibe/{talibeId}', name: 'update_talibe_presence', methods: ['POST'])]
public function modifierTalibePresence(EntityManagerInterface $entityManager, TalibeRepository $talibeRepository, int $talibeId, Security $security): JsonResponse {
    
    $user = $security->getUser();
    if (!$user || !in_array('ROLE_DAHRA', $user->getRoles())) {
        return new JsonResponse(['message' => 'Accès refusé'], JsonResponse::HTTP_FORBIDDEN);
    }

    $talibe = $talibeRepository->find($talibeId);
    if (!$talibe) {
        return new JsonResponse(['message' => 'Talibe non trouvé'], JsonResponse::HTTP_NOT_FOUND);
    }

    try {
        $talibe->setPresenceTalibe('sortie');
        $entityManager->flush();
    } catch (\InvalidArgumentException $e) {
        return new JsonResponse(['message' => $e->getMessage()], JsonResponse::HTTP_BAD_REQUEST);
    }

    return new JsonResponse(['message' => 'Choukrane cela signifie que le Talibe nest plus dans le dahra !']);
}

/**
 * @OA\Get(
 *     path="/api/lister-talibe",
 *     summary="Liste tous les Talibes",
 *     description="Récupère une liste de tous les Talibes enregistrés dans la base de données.",
 *     @OA\Response(
 *         response=200,
 *         description="Liste des Talibes obtenue avec succès",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="prenom", type="string", example="Modou"),
 *                 @OA\Property(property="nom", type="string", example="Diop"),
 *                 @OA\Property(property="age", type="integer", example=12),
 *                 @OA\Property(property="adresse", type="string", example="Adresse du Talibe"),
 *                 @OA\Property(property="situation", type="string", example="Orphelin"),
 *                 @OA\Property(property="description", type="string", example="Description du Talibe"),
 *                 @OA\Property(property="image", type="string", example="url_image"),
 *                 @OA\Property(property="datearrivetalibe", type="string", format="date-time", example="2024-01-01"),
 *                 @OA\Property(property="dahraNom", type="string", example="Nom du Dahra")
 *             )
 *         )
 *     )
 * )
 */
#[Route('/lister-talibe', name: 'lister_talibe', methods: ['GET'])]
public function listerTalibe(EntityManagerInterface $em,Request $request): JsonResponse
{
    $talibes = $em->getRepository(Talibe::class)->findAll();

    $data = [];
    foreach ($talibes as $talibe) {
        $dahra = $talibe->getDahra();
        $dahraNom = $dahra ? $dahra->getNom() : null;
        $dateArriveTalibe = $talibe->getDateArriveTalibe();

        // Formater la date d'arrivée au format Y-m-d
        $formattedDateArriveTalibe = $dateArriveTalibe ? $dateArriveTalibe->format('Y-m-d') : null;
        $imageFilename = $dahra->getImageFilename();
        $image = $imageFilename ? '/uploads/dahras/' . $imageFilename : null;
        $data[] = [
            'id' => $talibe->getId(),
            'prenom' => $talibe->getPrenom(),
            'nom' => $talibe->getNom(),
            'age' => $talibe->getAge(),
            'adresse' => $talibe->getAdresse(),
            'situation' => $talibe->getSituation(),
            'description' => $talibe->getDescription(),
            'image' => $talibe->getImage(),
            'datearrivetalibe' => $formattedDateArriveTalibe,
            'dahraNom' => $dahraNom,
            'imageFilename' => $image,
        ];
    }

    return new JsonResponse($data, JsonResponse::HTTP_OK);
}

/**
 * @OA\Get(
 *     path="/api/mes-dons",
 *     summary="Obtient les dons associés à l'utilisateur connecté",
 *     description="Récupère une liste de tous les dons faits par l'utilisateur connecté ou associés à sa Dahra.",
 *     security={{ "bearerAuth": {} }},
 *     @OA\Response(
 *         response=200,
 *         description="Liste des dons obtenue avec succès",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="typeDon", type="string", example="Nourriture"),
 *                 @OA\Property(property="adresseProvenance", type="string", example="123 Rue Exemple"),
 *                 @OA\Property(property="descriptionDon", type="string", example="Riz, pâtes, huile"),
 *                 @OA\Property(property="disponibiliteDon", type="string", example="Disponible"),
 *                 @OA\Property(property="date", type="string", format="date-time", example="2024-01-20T14:30:00"),
 *                 @OA\Property(property="status", type="string", example="En attente")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Utilisateur non connecté"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Dahra non associé à cet utilisateur"
 *     )
 * )
 */

#[Route('/mes-dons', name: 'mes_dons', methods: ['GET'])]
public function mesDons(EntityManagerInterface $em, Security $security): JsonResponse
{
    $user = $security->getUser();
   
    if (!$user) {
        return new JsonResponse(['message' => 'Utilisateur non connecté'], JsonResponse::HTTP_UNAUTHORIZED);
    }

    
    $dahra = $user->getDahras();
    if (!$dahra) {
        return new JsonResponse(['message' => 'Dahra non associé à cet utilisateur'], JsonResponse::HTTP_NOT_FOUND);
    }

    $dons = $em->getRepository(FaireDon::class)->findBy(['dahra' => $dahra]);

    $donsArray = [];
    foreach ($dons as $don) {
        $donsArray[] = [
            'id' => $don->getId(),
            'typeDon' => $don->getTypeDon(),
            'adresseProvenance' => $don->getAdresseProvenance(),
            'descriptionDon' => $don->getDescriptionDon(),
            'disponibiliteDon' => $don->getDisponibiliteDon(),
            'date' => $don->getDate()->format('Y-m-d H:i:s'),
            'status' => $don->getStatus(),
            
        ];
    }

    return new JsonResponse($donsArray);
}
/**
 * @OA\Get(
 *     path="/api/recherche-dahra",
 *     summary="Recherche de Dahras",
 *     description="Permet aux utilisateurs de rechercher des Dahras par région, adresse ou nom.",
 *     @OA\Parameter(
 *         name="region",
 *         in="query",
 *         description="La région pour filtrer les Dahras",
 *         required=false,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *         name="adresse",
 *         in="query",
 *         description="L'adresse pour filtrer les Dahras",
 *         required=false,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *         name="nom",
 *         in="query",
 *         description="Le nom pour filtrer les Dahras",
 *         required=false,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Liste des Dahras correspondant aux critères de recherche",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="nom", type="string", example="Dahra Al Azhar"),
 *                 @OA\Property(property="region", type="string", example="Dakar"),
 *                 @OA\Property(property="adresse", type="string", example="123 Boulevard de la République"),
 *                 @OA\Property(property="nomOuztas", type="string", example="Oustaz Alioune"),
 *                 @OA\Property(property="numerotelephoneouztas", type="string", example="781234567")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Aucun Dahra trouvé",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Aucun Dahra trouvé")
 *         )
 *     )
 * )
 */


     #[Route("/recherche-dahra", name:"recherche-dahra", methods:["GET"])]
     
    public function searchDahra(Request $request, DahraRepository $dahraRepository): JsonResponse
    {
        $region = $request->query->get('region');
        $adresse = $request->query->get('adresse');
        $nom = $request->query->get('nom');
        $criteria = [];
        if ($region) {
            $criteria['region'] = $region;
        }
        if ($adresse) {
            $criteria['adresse'] = $adresse;
        }

        if ($nom) {
            $criteria['nom'] = $nom;
        }
        $dahras = $dahraRepository->findBy($criteria);

        if (!$dahras) {
            return $this->json(['message' => 'Aucun Dahra trouvé'], JsonResponse::HTTP_NOT_FOUND);
        }

        $data = [];
        foreach ($dahras as $dahra) {
            $data[] = [
                'id' => $dahra->getId(),
                'nom' => $dahra->getNom(),
                'region' => $dahra->getRegion(),
                'adresse' => $dahra->getAdresse(),
                'nomOuztas' => $dahra->getNomOuztas(),
                'numerotelephoneouztas' => $dahra->getNumeroTelephoneOuztas(),
            ];
        }

        return $this->json($data);
    }


    #[Route("/recherche-talibe", name: "recherche-talibe", methods: ["GET"])]
public function searchTalibe(Request $request, TalibeRepository $talibeRepository): JsonResponse
{
    $nom = $request->query->get('nom');
    $prenom = $request->query->get('prenom');
    $age = $request->query->get('age');
    $adresse = $request->query->get('adresse');
    $criteria = [];

    if ($nom) {
        $criteria['nom'] = $nom;
    }

    if ($prenom) {
        $criteria['prenom'] = $prenom;
    }

    if ($age) {
        $criteria['age'] = $age;
    }

    if ($adresse) {
        $criteria['adresse'] = $adresse;
    }

    $talibes = $talibeRepository->findBy($criteria);

    if (!$talibes) {
        return $this->json(['message' => 'Aucun Talibe trouvé'], JsonResponse::HTTP_NOT_FOUND);
    }

    $data = [];
    foreach ($talibes as $talibe) {
        $data[] = [
            'id' => $talibe->getId(),
            'nom' => $talibe->getNom(),
            'prenom' => $talibe->getPrenom(),
            'age' => $talibe->getAge(),
            'adresse' => $talibe->getAdresse(),
           
        ];
    }

    return $this->json($data);
}

/**
 * @OA\Get(
 *     path="/lister-mes-talibes",
 *     summary="Lister les talibés associés à l'utilisateur",
 *     description="Permet à l'utilisateur d'obtenir la liste des talibés associés à ses Dahras.",
 *     @OA\Response(
 *         response=200,
 *         description="Liste des talibés",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="prenom", type="string", example="John"),
 *                 @OA\Property(property="nom", type="string", example="Doe"),
 *                 @OA\Property(property="age", type="integer", example=12),
 *                 @OA\Property(property="adresse", type="string", example="123 Rue de la République"),
 *                 @OA\Property(property="situation", type="string", example="En cours d'étude"),
 *                 @OA\Property(property="description", type="string", example="Talibé studieux"),
 *                 @OA\Property(property="image", type="string", example="talibe.jpg"),
 *                 @OA\Property(property="datearrivetalibe", type="string", example="2023-01-01"),
 *             )
 *         )
 *     ),
 *     @OA\Response(response=403, description="Accès non autorisé"),
 *     security={{"bearerAuth": {}}}
 * )
 */

    #[Route('/lister-mes-talibes', name: 'lister_mes_talibes', methods: ['GET'])]
public function listerMesTalibes(EntityManagerInterface $em, Security $security,Request $request): JsonResponse
{
    
    $user = $security->getUser();
   
    
    if (!$user || !$user->getDahras()) {
        return new JsonResponse(['error' => 'Accès non autorisé'], Response::HTTP_FORBIDDEN);
    }


    $dahras = $user->getDahras();
    $data = [];
    
    
    
    foreach ($dahras as $dahra) {
   
        $talibes = $em->getRepository(Talibe::class)->findBy(['Dahra' => $dahra]);
    
        foreach ($talibes as $talibe) {
            $data[] = [
                'id' => $talibe->getId(),
                'prenom' => $talibe->getPrenom(),
                'nom' => $talibe->getNom(),
                'age' => $talibe->getAge(),
                'adresse' => $talibe->getAdresse(),
                'situation' => $talibe->getSituation(),
                'description' => $talibe->getDescription(),
                'image' => $talibe->getImage(),
                'datearrivetalibe' => $talibe->getDateArriveTalibe(),
                'imageFilename' => $request->getSchemeAndHttpHost() . '/uploads/talibes/' . $talibe->getImageFilename(),
            ];
        }
    }

    return new JsonResponse($data);
}

/**
 * @OA\Delete(
 *     path="/dahra/supprimer-talibe/{id}",
 *     summary="Supprimer un talibé du Dahra",
 *     description="Permet à un Dahra de supprimer un talibé de son propre Dahra.",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID du talibé à supprimer",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Talibé supprimé avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Talibé supprimé avec succès")
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Accès refusé"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Talibé non trouvé"
 *     ),
 *     security={
 *         {"bearerAuth": {}}
 *     }
 * )
 */
#[Route('/dahra/supprimer-talibe/{id}', name:'supprimer_talibe', methods:['DELETE'])]
public function supprimerTalibe(int $id, EntityManagerInterface $entityManager, Security $security): JsonResponse
{
    $user = $security->getUser();

    if (!$user || !in_array('ROLE_DAHRA', $user->getRoles())) {
        return new JsonResponse(['message' => 'Accès refusé'], JsonResponse::HTTP_FORBIDDEN);
    }

    $dahra = $user->getDahras()->first();
    if (!$dahra) {
        return new JsonResponse(['message' => 'Dahra non trouvé'], JsonResponse::HTTP_NOT_FOUND);
    }

    $talibe = $entityManager->getRepository(Talibe::class)->find($id);

    if (!$talibe) {
        return new JsonResponse(['message' => 'Talibé non trouvé'], JsonResponse::HTTP_NOT_FOUND);
    }

    if ($talibe->getDahra() !== $dahra) {
        return new JsonResponse(['message' => 'Ce talibé ne fait pas partie de votre Dahra'], JsonResponse::HTTP_FORBIDDEN);
    }

    $entityManager->remove($talibe);
    $entityManager->flush();

    return new JsonResponse(['message' => 'Talibé supprimé avec succès'], JsonResponse::HTTP_OK);
}






/**
 * @OA\Post(
 *     path="/api/inscription/dahra",
 *     summary="Enregistrer un nouveau Dahra",
 *     description="Permet d'enregistrer un nouveau Dahra avec les informations fournies.",
 *     @OA\RequestBody(
 *         required=true,
 *         description="Données nécessaires pour l'enregistrement d'un Dahra",
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"email", "password", "nom", "nomOuztas", "adresse", "region", "numeroTelephoneOuztas","numeroTelephone", "nombreTalibe", "imageFile"},
 *                 @OA\Property(property="email", type="string", format="email", example="contact@gmail.com"),
 *                 @OA\Property(property="numeroTelephone", type="string", example="783718472"),
 *                 @OA\Property(property="password", type="string", format="password", example="Passer123"),
 *                 @OA\Property(property="nom", type="string", example="Dahra Al Azhar"),
 *                 @OA\Property(property="nomOuztas", type="string", example="Cheikh Modou"),
 *                 @OA\Property(property="adresse", type="string", example="Marabouts"),
 *                 @OA\Property(property="region", type="string", example="Dakar"),
 *                 @OA\Property(property="numeroTelephoneOuztas", type="string", example="783718472"),
 *                 @OA\Property(property="nombreTalibe", type="integer", example=100),
 *                 @OA\Property(property="imageFile", type="string", format="binary", description="Fichier image du Dahra")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Dahra enregistré avec succès",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="nom", type="string", example="Dahra Al Azhar"),
 *             @OA\Property(property="region", type="string", example="Dakar"),
 *            
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Données invalides fournies"
 *     ),
 *     security={
 *         {"Bearer": {}}
 *     }
 * )
 * @IgnoreAnnotation("Security")
 */
#[Route('/dahra', name: 'api_dahra_register', methods: ['POST'])]
public function register(Request $request, UserPasswordHasherInterface $passwordHasher, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator, FileUploader $fileUploader): JsonResponse
{
    // Décoder les données JSON du corps de la requête
    $data = json_decode($request->getContent(), true);
    
    // Valider les données
    $constraints = new Assert\Collection([
        'email' => [new Assert\NotBlank(), new Assert\Email()],
        'password' => [new Assert\NotBlank()],
        'nom' => [new Assert\NotBlank(), new Assert\Type(['type' => 'string'])],
        'nomOuztas' => [new Assert\NotBlank(), new Assert\Type(['type' => 'string'])],
        'adresse' => [new Assert\NotBlank(), new Assert\Type(['type' => 'string'])],
        'region' => [new Assert\NotBlank(), new Assert\Type(['type' => 'string'])],
        'numeroTelephone' => [
            new Assert\NotBlank(),
            new Assert\Regex(['pattern' => '/^(77|78|76|70)\d{7}$/']),
        ],
        'numeroTelephoneOuztas' => [
            new Assert\NotBlank(),
            new Assert\Regex(['pattern' => '/^(77|78|76|70)\d{7}$/']),
        ],
        'nombreTalibe' => [new Assert\NotBlank(), new Assert\Type(['type' => 'integer'])],
    ]);

    $violations = $validator->validate($data, $constraints);

    if (count($violations) > 0) {
        return new JsonResponse(['errors' => (string) $violations], JsonResponse::HTTP_BAD_REQUEST);
    }

    // Créer et enregistrer l'utilisateur
    $user = new User();
    $user->setEmail($data['email']);
    $user->setNumeroTelephone($data['numeroTelephone']);
    $user->setPassword($passwordHasher->hashPassword($user, $data['password']));
    $user->setRoles(['ROLE_DAHRA']);
    $user->setIsActive(false);

    // Créer et enregistrer le Dahra
    $dahra = new Dahra();
    $dahra->setNom($data['nom']);
    $dahra->setNomOuztas($data['nomOuztas']);
    $dahra->setAdresse($data['adresse']);
    $dahra->setRegion($data['region']);
    $dahra->setNumeroTelephoneOuztas($data['numeroTelephoneOuztas']);
    $dahra->setNombreTalibe($data['nombreTalibe']);
    $dahra->setUser($user);

    // Gérer l'upload de l'image si elle est présente
    if (isset($data['imageFile'])) {
        $uploadedFilePath = $fileUploader->upload($data['imageFile']);
        $dahra->setImageFilename($uploadedFilePath);
    }

    // Persist et flush les entités
    $entityManager->persist($user);
    $entityManager->persist($dahra);
    $entityManager->flush();

    // Serialize le Dahra et retourne la réponse
    $responseData = $serializer->serialize($dahra, 'json');
    return new JsonResponse($responseData, Response::HTTP_CREATED, [], true);
}
}
