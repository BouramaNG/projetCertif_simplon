<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Dahra;
use App\Entity\Talibe;
use App\Entity\FaireDon;
use OpenApi\Annotations as OA;
use App\Repository\DahraRepository;
use App\Repository\TalibeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

  /**
 * @OA\Post(
 *     path="/api/register/dahra",
 *     summary="Enregistrer un nouveau Dahra",
 *     description="Permet d'enregistrer un nouveau Dahra avec les informations fournies.",
 *     @OA\RequestBody(
 *         required=true,
 *         description="Données nécessaires pour l'enregistrement d'un Dahra",
 *         @OA\JsonContent(
 *             required={"email", "password", "nom", "nomOuztas", "adresse", "region", "numeroTelephoneOuztas", "nombreTalibe"},
 *             @OA\Property(property="email", type="string", format="email", example="contact@dahraexample.com"),
 *             @OA\Property(property="password", type="string", format="password", example="StrongPassword123"),
 *             @OA\Property(property="nom", type="string", example="Dahra Al Azhar"),
 *             @OA\Property(property="nomOuztas", type="string", example="Cheikh Modou"),
 *             @OA\Property(property="adresse", type="string", example="123 Avenue des Marabouts"),
 *             @OA\Property(property="region", type="string", example="Dakar"),
 *             @OA\Property(property="numeroTelephoneOuztas", type="string", example="0123456789"),
 *             @OA\Property(property="nombreTalibe", type="integer", example=100)
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
 *     )
 * )
 */

#[Route('/api', name: 'api_')]
class DahraController extends AbstractController
{
 
    #[Route('/register/dahra', name: 'api_dahra_register', methods: ['POST'])]
    public function registerDahra(Request $request, UserPasswordHasherInterface $passwordHasher, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
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
    
        if (!isset($data['email'], $data['password'], $data['nom'], $data['nomOuztas'], $data['adresse'], $data['region'], $data['numeroTelephoneOuztas'], $data['nombreTalibe'])) {
            return new JsonResponse(['error' => 'Des champs obligatoires manques'], Response::HTTP_BAD_REQUEST);
        }
    
        $user = new User();
        $user->setEmail($data['email']);
        $user->setNumeroTelephone($data['numeroTelephone']);
        $user->setPassword($passwordHasher->hashPassword($user, $data['password']));
        $user->setRoles(['ROLE_DAHRA']);
        $user->setIsActive(false);
    
        $dahra = new Dahra();
        $dahra->setNom($data['nom']);
        $dahra->setNomOuztas($data['nomOuztas']);
        $dahra->setAdresse($data['adresse']);
        $dahra->setRegion($data['region']);
        $dahra->setNumeroTelephoneOuztas($data['numeroTelephoneOuztas']);
        $dahra->setNombreTalibe($data['nombreTalibe']);
        $dahra->setUser($user);
        
        $entityManager->persist($user);
        $entityManager->persist($dahra);
        $entityManager->flush();
    
        $responseData = $serializer->serialize($dahra, 'json');
        return new JsonResponse($responseData, Response::HTTP_CREATED, [], true);
    }
/**
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
 *         @OA\JsonContent(
 *             @OA\Property(property="nom", type="string", example="Nouveau Nom"),
 *             @OA\Property(property="nomOuztas", type="string", example="Nouveau Nom Ouztas"),
 *             @OA\Property(property="adresse", type="string", example="Nouvelle Adresse"),
 *             @OA\Property(property="region", type="string", example="Nouvelle Région"),
 *             @OA\Property(property="numeroTelephoneOuztas", type="string", example="Nouveau Numéro de Téléphone Ouztas"),
 *             @OA\Property(property="nombreTalibe", type="integer", example=50),
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
 *     security={
 *         {"bearerAuth": {}}
 *     }
 * )
 * 
 */

    
 #[Route('/modifier-dahra/{id}', name: 'api_dahra_modifier', methods: ['PUT'])]
 public function updateDahra(int $id, Request $request,ValidatorInterface $validator, UserPasswordHasherInterface $passwordHasher, SerializerInterface $serializer, EntityManagerInterface $entityManager): JsonResponse
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
     if (isset($data['nom'])) {
         $dahra->setNom($data['nom']);
     }
 
     if (isset($data['nomOuztas'])) {
         $dahra->setNomOuztas($data['nomOuztas']);
     }
 
     if (isset($data['adresse'])) {
         $dahra->setAdresse($data['adresse']);
     }
 
     if (isset($data['region'])) {
         $dahra->setRegion($data['region']);
     }
 
     if (isset($data['numeroTelephoneOuztas'])) {
         $dahra->setNumeroTelephoneOuztas($data['numeroTelephoneOuztas']);
     }
 
     if (isset($data['nombreTalibe'])) {
         $dahra->setNombreTalibe($data['nombreTalibe']);
     }
 
     $entityManager->flush();
 
     $responseData = $serializer->serialize($dahra, 'json');
     return new JsonResponse($responseData, Response::HTTP_OK, [], true);
 }
 

  

/**
 * @OA\Post(
 *     path="/api/dahra/add-talibe",
 *     summary="Ajouter un Talibe à un Dahra",
 *     description="Permet à un utilisateur avec le rôle Dahra d'ajouter un nouveau Talibe au Dahra.",
 *     @OA\RequestBody(
 *         required=true,
 *         description="Informations nécessaires pour l'ajout d'un Talibe",
 *         @OA\JsonContent(
 *             required={"nom", "prenom", "age", "adresse", "situation", "description", "datearrivetalibe"},
 *             @OA\Property(property="nom", type="string", example="Amadou"),
 *             @OA\Property(property="prenom", type="string", example="Diop"),
 *             @OA\Property(property="age", type="integer", example=12),
 *             @OA\Property(property="adresse", type="string", example="123 Rue de Dakar"),
 *             @OA\Property(property="situation", type="string", example="orphelin"),
 *             @OA\Property(property="description", type="string", example="Enfant calme et studieux"),
 *             @OA\Property(property="datearrivetalibe", type="string", format="date", example="2024-01-01"),
 *             @OA\Property(property="image", type="string", format="url", example="http://exemple.com/image.jpg", nullable=true),
 *             @OA\Property(property="presencetalibe", type="string", example="present", nullable=true)
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Talibe ajouté avec succès",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Choukrane vous avez ajouter avec succee un Talibe !"),
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

    #[Route('/dahra/add-talibe', name: 'add_talibe_to_dahra', methods: ['POST'])]
    public function addTalibeToDahra(Request $request, EntityManagerInterface $entityManager, Security $security, ValidatorInterface $validator): Response
    {
        $user = $security->getUser();
        
        if (!$user || !in_array('ROLE_DAHRA', $user->getRoles())) {
            return new JsonResponse(['message' => 'Accès refusé'], JsonResponse::HTTP_FORBIDDEN);
        }
    
        $user = $this->getUser();
        if (!$user || !$user->isActive()) {
            return new JsonResponse(['error' => 'Votre compte est bloqué. Vous ne pouvez pas ajouter de talibé.'], Response::HTTP_FORBIDDEN);
        }
    
        $dahra = $user->getDahras()->first();
        if (!$dahra) {
            return new JsonResponse(['message' => 'Dahra non trouvé'], JsonResponse::HTTP_NOT_FOUND);
        }
    
        $data = json_decode($request->getContent(), true);
    
        $talibe = new Talibe();
        $talibe->setNom($data['nom']);
        $talibe->setPrenom($data['prenom']);
        $talibe->setAge($data['age']);
        $talibe->setAdresse($data['adresse']);
        $talibe->setSituation($data['situation']);
        $talibe->setDescription($data['description']);
        $talibe->setImage($data['image'] ?? null);
    
        $dateArriveTalibe = \DateTime::createFromFormat('Y-m-d', $data['daterrivetalibe']);
        $talibe->setDateArriveTalibe($dateArriveTalibe);
    
        $talibe->setPresenceTalibe($data['presencetalibe'] ?? 'present');
    
        $talibe->setDahra($dahra);
    
        // Ajoutez les contraintes de validation ici
        $violations = $validator->validate($talibe);
    
        // Validation personnalisée pour vérifier le doublon de nom, prénom et âge
        $doublonViolation = $this->validateDoublon($talibe, $entityManager);
        if ($doublonViolation) {
            $violations->add($doublonViolation);
        }
    
        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[$violation->getPropertyPath()] = $violation->getMessage();
            }
    
            return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }
    
        $entityManager->persist($talibe);
        $entityManager->flush();
    
        return $this->json([
            'message' => 'Choukrane vous avez ajouté avec succès un Talibe !',
            'talibeId' => $talibe->getId()
        ], Response::HTTP_CREATED);
    }
    
    private function validateDoublon(Talibe $talibe, EntityManagerInterface $entityManager): ?ConstraintViolation
    {
       
        $existingTalibe = $entityManager->getRepository(Talibe::class)->findOneBy([
            'nom' => $talibe->getNom(),
            'prenom' => $talibe->getPrenom(),
            'age' => $talibe->getAge(),
        ]);
    
        if ($existingTalibe && $existingTalibe->getId() !== $talibe->getId()) {
           
            $message = sprintf(
                'Un talibé avec le même nom, prénom et âge existe déjà. Veuillez ajouter quelque chose au prénom pour les différencier.'
            );
    
            return new ConstraintViolation($message, null, [], $talibe, 'doublonTalibe', null);
        }
    
        return null;
    }


    // #[Route('/dahra/add-talibeTest', name: 'add_talibe_to_dahra', methods: ['POST'])]
    // public function addTalibeToDahraTest(Request $request, EntityManagerInterface $entityManager, Security $security, ValidatorInterface $validator): Response
    // {
    //     $user = $security->getUser();
        
    //     if (!$user || !in_array('ROLE_DAHRA', $user->getRoles())) {
    //         return new JsonResponse(['message' => 'Accès refusé'], JsonResponse::HTTP_FORBIDDEN);
    //     }
    
    //     $user = $this->getUser();
    //     if (!$user || !$user->isActive()) {
    //         return new JsonResponse(['error' => 'Votre compte est bloqué. Vous ne pouvez pas ajouter de talibé.'], Response::HTTP_FORBIDDEN);
    //     }
    
    //     $dahra = $user->getDahras()->first();
    //     if (!$dahra) {
    //         return new JsonResponse(['message' => 'Dahra non trouvé'], JsonResponse::HTTP_NOT_FOUND);
    //     }
    
    //     $data = json_decode($request->getContent(), true);
    
    //     $talibe = new Talibe();
    //     $talibe->setNom($data['nom']);
    //     $talibe->setPrenom($data['prenom']);
    //     $talibe->setAge($data['age']);
    //     $talibe->setAdresse($data['adresse']);
    //     $talibe->setSituation($data['situation']);
    //     $talibe->setDescription($data['description']);
    //     $talibe->setImage($data['image'] ?? null);
    
    //     $dateArriveTalibe = \DateTime::createFromFormat('Y-m-d', $data['datearrivetalibe']);
    //     $talibe->setDateArriveTalibe($dateArriveTalibe);
    
    //     $talibe->setPresenceTalibe($data['presencetalibe'] ?? 'present');
    
    //     $talibe->setDahra($dahra);
    
    //     // Ajoutez les contraintes de validation ici
    //     $violations = $validator->validate($talibe);
    
    //     // Validation personnalisée pour vérifier le doublon de nom, prénom et âge
    //     $doublonViolation = $this->validateDoublon($talibe, $entityManager);
    //     if ($doublonViolation) {
    //         $violations->add($doublonViolation);
    //     }
    
    //     if (count($violations) > 0) {
    //         $errors = [];
    //         foreach ($violations as $violation) {
    //             $errors[$violation->getPropertyPath()] = $violation->getMessage();
    //         }
    
    //         return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
    //     }
    
    //     $entityManager->persist($talibe);
    //     $entityManager->flush();
    
    //     return $this->json([
    //         'message' => 'Choukrane vous avez ajouté avec succès un Talibe !',
    //         'talibeId' => $talibe->getId()
    //     ], Response::HTTP_CREATED);
    // }







/**
 * @Route("/modifier_info_talibe/{id}", name="modifier_talibe", methods={"GET", "PUT"})
 * @OA\Put(
 *     path="/modifier_info_talibe/{id}",
 *     summary="Modifier les informations du talibé",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="nom", type="string"),
 *             @OA\Property(property="prenom", type="string"),
 *             @OA\Property(property="age", type="integer"),
 *             @OA\Property(property="adresse", type="string"),
 *             @OA\Property(property="situation", type="string"),
 *             @OA\Property(property="description", type="string"),
 *         )
 *     ),
 *     @OA\Response(
 *         response="200",
 *         description="Informations du talibé modifiées avec succès"
 *     ),
 *     @OA\Response(
 *         response="404",
 *         description="Talibé non trouvé"
 *     )
 * )
 */


    #[Route('/modifier_info_talibe/{id}', name: 'modifier', methods: ['GET', 'PUT'])]
    public function modifierTalibe(int $id,Security $security, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {

        $user = $security->getUser();
        
        if (!$user || !in_array('ROLE_DAHRA', $user->getRoles())) {
            return new JsonResponse(['message' => 'Accès refusé'], JsonResponse::HTTP_FORBIDDEN);
        }
        $talibe = $entityManager->getRepository(Talibe::class)->find($id);

        if (!$talibe) {
            return new JsonResponse(['message' => 'Talibé non trouvé'], JsonResponse::HTTP_NOT_FOUND);
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

        $data = json_decode($request->getContent(), true);

      
        $talibe->setNom($data['nom'] ?? $talibe->getNom());
        $talibe->setPrenom($data['prenom'] ?? $talibe->getPrenom());
        $talibe->setAge($data['age'] ?? $talibe->getAge());
        $talibe->setAdresse($data['adresse'] ?? $talibe->getAdresse());
        $talibe->setSituation($data['situation'] ?? $talibe->getSituation());
        $talibe->setDescription($data['description'] ?? $talibe->getDescription());
        $entityManager->flush();

        return $this->json([
            'message' => 'Informations du talibé modifiées avec succès',
            'talibeId' => $talibe->getId()
        ], JsonResponse::HTTP_OK);
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
public function listerTalibe(EntityManagerInterface $em): JsonResponse
{
    $talibes = $em->getRepository(Talibe::class)->findAll();

    $data = [];
    foreach ($talibes as $talibe) {
        $dahra = $talibe->getDahra();
        $dahraNom = $dahra ? $dahra->getNom() : null;
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
            'dahraNom' => $dahraNom,
            
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
public function listerMesTalibes(EntityManagerInterface $em, Security $security): JsonResponse
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



#[Route('/add-talibeTest', name: 'add_talibe_to_dahra', methods: ['POST'])]
public function addTalibeToDahraTest(Request $request, EntityManagerInterface $entityManager, Security $security, ValidatorInterface $validator): Response
{
    
    $data = json_decode($request->getContent(), true);

    $talibe = new Talibe();
    $talibe->setNom($data['nom']);
    $talibe->setPrenom($data['prenom']);
    $talibe->setAge($data['age']);
    $talibe->setAdresse($data['adresse']);
    $talibe->setSituation($data['situation']);
    $talibe->setDescription($data['description']);
    $talibe->setImage($data['image'] ?? null);

    $dateArriveTalibe = \DateTime::createFromFormat('Y-m-d', $data['datearrivetalibe']);
    $talibe->setDateArriveTalibe($dateArriveTalibe);

    $talibe->setPresenceTalibe($data['presencetalibe'] ?? 'present');

   
    $entityManager->persist($talibe);
    $entityManager->flush();

    return $this->json([
        'message' => 'Choukrane vous avez ajouté avec succès un Talibe !',
        'talibeId' => $talibe->getId()
    ], Response::HTTP_CREATED);
}

}
