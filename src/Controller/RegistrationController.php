<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Marraine;
use OpenApi\Annotations as OA;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
 /**
 * @OA\Post(
 *     path="/api/inscrire-donateur",
 *     summary="Inscrire un nouveau donateur",
 *     description="Permet d'inscrire un nouveau donateur avec les informations fournies.",
 *     @OA\RequestBody(
 *         required=true,
 *         description="Données nécessaires pour l'inscription d'un donateur",
 *         @OA\JsonContent(
 *             required={"nom", "prenom", "email", "password", "adresse", "numeroTelephone"},
 *             @OA\Property(property="nom", type="string", example="Doe"),
 *             @OA\Property(property="prenom", type="string", example="John"),
 *             @OA\Property(property="email", type="string", format="email", example="johndoe@example.com"),
 *             @OA\Property(property="password", type="string", format="password", example="Password123"),
 *             @OA\Property(property="adresse", type="string", example="123 Rue Exemple"),
 *             @OA\Property(property="numeroTelephone", type="string", example="0123456789")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Inscription du donateur réussie",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Inscription Donateur avec succès")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Données invalides fournies"
 *     )
 * )
 */

#[Route('/api', name: 'api_')]
class RegistrationController extends AbstractController
{
    #[Route('/inscrire-donateur', name: 'inscrire_donateur', methods: ['POST'])]
    public function register(EntityManagerInterface $em, Request $request, UserPasswordHasherInterface $passwordHasher, ValidatorInterface $validator, RoleRepository $roleRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
    
        $constraints = new Assert\Collection([
            'nom' => [
                new Assert\NotBlank(),
                new Assert\Type(['type' => 'string']),
                new Assert\Regex('/^[a-zA-Z]+$/'),
                new Assert\Length(['min' => 2, 'max' => 20]),
            ],
            'prenom' => [
                new Assert\NotBlank(),
                new Assert\Type(['type' => 'string']),
                new Assert\Regex('/^[a-zA-Z]+$/'),
                new Assert\Length(['min' => 2, 'max' => 20]),
            ],
            'email' => [
                new Assert\NotBlank(),
                new Assert\Email([
                    'message' => 'L\'adresse email "{{ value }}" n\'est pas valide.'
                ]),
                new Assert\Regex([
                    'pattern' => '/^[a-zA-Z]{3,}\d*@(gmail\.com|yahoo\.com|hotmail\.com)$/',
                    'message' => 'L\'email doit contenir au moins 3 lettres avant le @ et doit être de la forme "xxx@gmail.com", "xxx@yahoo.com" ou "xxx@hotmail.com".'
                ])
            ],
            'password' => [
                new Assert\NotBlank(),
                new Assert\Length(['min' => 6, 'max' => 15]),
                new Assert\Regex([
                    'pattern' => '/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,15}$/',
                    'message' => 'Le mot de passe doit contenir au moins 6 caractères, au moins une lettre, un chiffre et un caractère spécial.'
                ])
            ],
            'adresse' => [new Assert\NotBlank(), new Assert\Type(['type' => 'string']), new Assert\Regex('/^[a-zA-Z]+$/')],
            'numeroTelephone' => [new Assert\NotBlank(), new Assert\Length(['min' => 5])],
        ]);
    
        $violations = $validator->validate($data, $constraints);
        if (count($violations) > 0) {
            return $this->json(['errors' => (string) $violations], JsonResponse::HTTP_BAD_REQUEST);
        }
    
        // Récupérer le rôle "DONATEUR" depuis la base de données
        $roleDonateur = $roleRepository->findOneBy(['nomRole' => 'DONATEUR']);
    
        if (!$roleDonateur) {
            return $this->json(['error' => 'Le rôle "DONATEUR" n\'existe pas.'], JsonResponse::HTTP_BAD_REQUEST);
        }
    
        $user = new User();
        $user->setNom($data['nom']);
        $user->setPrenom($data['prenom']);
        $user->setEmail($data['email']);
        $user->setPassword($passwordHasher->hashPassword($user, $data['password']));
        $user->setAdresse($data['adresse']);
        $user->setNumeroTelephone($data['numeroTelephone']);
        $user->setRoleEntity($roleDonateur);
        $user->setRoles(['ROLE_DONATEUR']);
    
        $em->persist($user);
        $em->flush();
    
        $responseData = [
            'email' => $user->getEmail(),
            'nom' => $user->getNom(),
            'prenom' => $user->getPrenom(),
            'adresse' => $user->getAdresse(),
            'numeroTelephone' => $user->getNumeroTelephone(),
            'message' => 'Inscription du donateur réussie',
        ];
    
        return $this->json($responseData, JsonResponse::HTTP_CREATED);
    }
    



/**
 * @OA\Put(
 *     path="/modifier-donateur/{id}",
 *     summary="Modifier les informations d'un donateur",
 *     description="Permet à un donateur de mettre à jour ses informations.",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID du donateur à mettre à jour",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         description="Données nécessaires pour mettre à jour les informations du donateur",
 *         @OA\JsonContent(
 *             required={"nom", "prenom", "email", "adresse", "numeroTelephone"},
 *             @OA\Property(property="nom", type="string", minLength=4),
 *             @OA\Property(property="prenom", type="string", minLength=4),
 *             @OA\Property(property="email", type="string", format="email"),
 *             @OA\Property(property="adresse", type="string"),
 *             @OA\Property(property="numeroTelephone", type="string", minLength=5),
 *             @OA\Property(property="password", type="string", minLength=1),
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Informations du donateur modifiées avec succès",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Informations du donateur modifiées avec succès")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Données invalides fournies"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Donateur non trouvé"
 *     )
 * )
 */

#[Route('/modifier-donateur/{id}', name: 'modifier_donateur', methods: ['PUT'])]
public function updateDonateur(int $id, Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
{
    $data = json_decode($request->getContent(), true);
    $constraints = new Assert\Collection([
        'nom' => [
            new Assert\NotBlank(),
            new Assert\Type(['type' => 'string']),
            new Assert\Regex('/^[a-zA-Z]+$/'),
            new Assert\Length(['min' => 2, 'max' => 20]),
        ],
        'prenom' => [
            new Assert\NotBlank(),
            new Assert\Type(['type' => 'string']),
            new Assert\Regex('/^[a-zA-Z]+$/'),
            new Assert\Length(['min' => 2, 'max' => 20]),
        ],
        'email' => [
            new Assert\NotBlank(),
            new Assert\Email([
                'message' => 'L\'adresse email "{{ value }}" n\'est pas valide.'
            ]),
            new Assert\Regex([
                'pattern' => '/^[a-zA-Z]{3,}\d*@(gmail\.com|yahoo\.com|hotmail\.com)$/',
                'message' => 'L\'email doit contenir au moins 3 lettres avant le @ et doit être de la forme "xxx@gmail.com", "xxx@yahoo.com" ou "xxx@hotmail.com".'
            ])
        ],
        'adresse' => [new Assert\NotBlank(), new Assert\Type(['type' => 'string']), new Assert\Regex('/^[a-zA-Z]+$/')],
        'numeroTelephone' => [new Assert\NotBlank(), new Assert\Length(['min' => 5])],
        'password' => [
            new Assert\NotBlank(),
            new Assert\Length(['min' => 6, 'max' => 15]),
            new Assert\Regex([
                'pattern' => '/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,15}$/',
                'message' => 'Le mot de passe doit contenir au moins 6 caractères, au moins une lettre, un chiffre et un caractère spécial.'
            ])
        ],  
    ]);

    $violations = $validator->validate($data, $constraints);
    if (count($violations) > 0) {
        return $this->json(['errors' => (string) $violations], JsonResponse::HTTP_BAD_REQUEST);
    }

    $donateur = $entityManager->getRepository(User::class)->find($id);

    if (!$donateur) {
        return new JsonResponse(['error' => 'Donateur not found'], JsonResponse::HTTP_NOT_FOUND);
    }

    $donateur->setNom($data['nom']);
    $donateur->setPrenom($data['prenom']);
    $donateur->setEmail($data['email']);
    $donateur->setAdresse($data['adresse']);
    $donateur->setNumeroTelephone($data['numeroTelephone']);

    if (isset($data['password'])) {
        $donateur->setPassword($passwordHasher->hashPassword($donateur, $data['password']));
    }

    $entityManager->flush();

    return $this->json(['message' => 'Informations du donateur modifiées avec succès'], JsonResponse::HTTP_OK);
}






/**
 * @OA\Post(
 *     path="/api/devenir-marraine",
 *     summary="Inscrire un nouveau devenir-marraine",
 *     description="Permet d'inscrire un devenir-marraine",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"nom", "prenom", "email", "password", "adresse", "numeroTelephone"},
 *             @OA\Property(property="nom", type="string", example="Dupont"),
 *             @OA\Property(property="prenom", type="string", example="Jean"),
 *             @OA\Property(property="email", type="string", format="email", example="jean.dupont@example.com"),
 *             @OA\Property(property="password", type="string", example="yourpassword"),
 *             @OA\Property(property="adresse", type="string", example="123 rue de la Paix"),
 *             @OA\Property(property="numeroTelephone", type="string", example="775003108")
 *            
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Marraine inscrit avec succès"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Données invalides"
 *     )
 * )
 */
#[Route('/devenir-marraine', name: 'devenir_marraine', methods: ['POST'])]
public function devenirMarraine(EntityManagerInterface $em, Request $request, UserPasswordHasherInterface $passwordHasher, ValidatorInterface $validator, RoleRepository $roleRepository): JsonResponse
{
    $data = json_decode($request->getContent(), true);

    $constraints = new Assert\Collection([
        'nom' => [
            new Assert\NotBlank(),
            new Assert\Type(['type' => 'string']),
            new Assert\Regex('/^[a-zA-Z]+$/'),
            new Assert\Length(['min' => 2, 'max' => 20]),
        ],
        'prenom' => [
            new Assert\NotBlank(),
            new Assert\Type(['type' => 'string']),
            new Assert\Regex('/^[a-zA-Z]+$/'),
            new Assert\Length(['min' => 2, 'max' => 20]),
        ],
        'email' => [
            new Assert\NotBlank(),
            new Assert\Email([
                'message' => 'L\'adresse email "{{ value }}" n\'est pas valide.'
            ]),
            new Assert\Regex([
                'pattern' => '/^[a-zA-Z]{3,}\d*@(gmail\.com|yahoo\.com|hotmail\.com)$/',
                'message' => 'L\'email doit contenir au moins 3 lettres avant le @ et doit être de la forme "xxx@gmail.com", "xxx@yahoo.com" ou "xxx@hotmail.com".'
            ])
        ],
        'password' => [
            new Assert\NotBlank(),
            new Assert\Length(['min' => 6, 'max' => 15]),
            new Assert\Regex([
                'pattern' => '/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,15}$/',
                'message' => 'Le mot de passe doit contenir au moins 6 caractères, au moins une lettre, un chiffre et un caractère spécial.'
            ])
        ],
        'adresse' => [new Assert\NotBlank(), new Assert\Type(['type' => 'string']), new Assert\Regex('/^[a-zA-Z]+$/')],
        'numeroTelephone' => [new Assert\NotBlank(), new Assert\Length(['min' => 5])],
    ]);

    $violations = $validator->validate($data, $constraints);
    
    if (count($violations) > 0) {
        return new JsonResponse(['errors' => (string) $violations], JsonResponse::HTTP_BAD_REQUEST);
    }

    $client = HttpClient::create();
    $response = $client->request('GET', 'https://api.ipgeolocation.io/ipgeo?apiKey=14f46a4dfa064abdab4b0b6a995921d2');
    $geoData = $response->toArray();
    $ipAddress = $geoData['ip'];
    $latitude = $geoData['latitude'];
    $longitude = $geoData['longitude'];

    $roleMarraine = $roleRepository->findOneBy(['nomRole' => 'MARRAINE']);

    if (!$roleMarraine) {
        return $this->json(['error' => 'Le rôle "MARRAINE" n\'existe pas.'], JsonResponse::HTTP_BAD_REQUEST);
    }

    $user = new User();
    $user->setNom($data['nom']);
    $user->setPrenom($data['prenom']);
    $user->setEmail($data['email']);
    $user->setPassword($passwordHasher->hashPassword($user, $data['password']));
    $user->setAdresse($data['adresse']);
    $user->setNumeroTelephone($data['numeroTelephone']);
    $user->setRoleEntity($roleMarraine);
    $user->setRoles(['ROLE_MARRAINE']);

    $em->persist($user);
    $em->flush();

    $responseData = [
        'email' => $user->getEmail(),
        'nom' => $user->getNom(),
        'prenom' => $user->getPrenom(),
        'adresse' => $user->getAdresse(),
        'numeroTelephone' => $user->getNumeroTelephone(),
        'ip_address' => $geoData['ip'],
        'latitude' => $geoData['latitude'],
        'longitude' => $geoData['longitude'],
        'message' => 'Inscription de la marraine réussie',
    ];

    return $this->json($responseData, JsonResponse::HTTP_CREATED);
}



/**
 * @OA\Put(
 *     path="/modifier-marraine/{id}",
 *     summary="Modifier les informations de la marraine",
 *     description="Permet à la marraine de mettre à jour ses informations.",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID de la marraine",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         description="Nouvelles données de la marraine",
 *         @OA\JsonContent(
 *             required={"nom", "prenom", "email", "adresse", "numeroTelephone", "password"},
 *             @OA\Property(property="nom", type="string", example="Doe"),
 *             @OA\Property(property="prenom", type="string", example="Jane"),
 *             @OA\Property(property="email", type="string", format="email", example="jane.doe@example.com"),
 *             @OA\Property(property="adresse", type="string", example="123 Rue de la Liberté"),
 *             @OA\Property(property="numeroTelephone", type="string", example="1234567890"),
 *             @OA\Property(property="password", type="string", format="password", example="newpassword"),
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Informations de la marraine modifiées avec succès",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Informations de la marraine modifiées avec succès")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Erreurs de validation des données"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Marraine non trouvée"
 *     )
 * )
 */

#[Route('/modifier-marraine/{id}', name: 'modifier_marraine', methods: ['PUT'])]
public function modifierMarraine(int $id, Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
{
    $data = json_decode($request->getContent(), true);

    $constraints = new Assert\Collection([
            'nom' => [
            new Assert\NotBlank(),
            new Assert\Type(['type' => 'string']),
            new Assert\Regex('/^[a-zA-Z]+$/'),
            new Assert\Length(['min' => 2, 'max' => 20]),
        ],
        'prenom' => [
            new Assert\NotBlank(),
            new Assert\Type(['type' => 'string']),
            new Assert\Regex('/^[a-zA-Z]+$/'),
            new Assert\Length(['min' => 2, 'max' => 20]),
        ],
        'email' => [
            new Assert\NotBlank(),
            new Assert\Email([
                'message' => 'L\'adresse email "{{ value }}" n\'est pas valide.'
            ]),
            new Assert\Regex([
                'pattern' => '/^[a-zA-Z]{3,}\d*@(gmail\.com|yahoo\.com|hotmail\.com)$/',
                'message' => 'L\'email doit contenir au moins 3 lettres avant le @ et doit être de la forme "xxx@gmail.com", "xxx@yahoo.com" ou "xxx@hotmail.com".'
            ])
        ],
        'adresse' => [new Assert\NotBlank(), new Assert\Type(['type' => 'string'])],
        'numeroTelephone' => [new Assert\NotBlank(), new Assert\Length(['min' => 5])],
        'password' => [
            new Assert\NotBlank(),
            new Assert\Length(['min' => 6, 'max' => 15]),
            new Assert\Regex([
                'pattern' => '/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,15}$/',
                'message' => 'Le mot de passe doit contenir au moins 6 caractères, au moins une lettre, un chiffre et un caractère spécial.'
            ])
        ],
    ]);

    $violations = $validator->validate($data, $constraints);
    if (count($violations) > 0) {
        return $this->json(['errors' => (string) $violations], JsonResponse::HTTP_BAD_REQUEST);
    }

    $marraine = $entityManager->getRepository(User::class)->find($id);

    if (!$marraine) {
        return new JsonResponse(['error' => 'Marraine not found'], JsonResponse::HTTP_NOT_FOUND);
    }

    $marraine->setNom($data['nom']);
    $marraine->setPrenom($data['prenom']);
    $marraine->setEmail($data['email']);
    $marraine->setAdresse($data['adresse']);
    $marraine->setNumeroTelephone($data['numeroTelephone']);

    if (isset($data['password'])) {
        $marraine->setPassword($passwordHasher->hashPassword($marraine, $data['password']));
    }

    $entityManager->flush();

    return $this->json(['message' => 'Informations de la marraine modifiées avec succès'], JsonResponse::HTTP_OK);
}






/**
 * @OA\Post(
 *     path="/api/inscrire-admin",
 *     summary="Inscrire un nouveau inscrire-admin",
 *     description="Permet d'inscrire un inscrire-admin",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"nom", "prenom", "email", "password", "adresse", "numeroTelephone"},
 *             @OA\Property(property="nom", type="string", example="Dupont"),
 *             @OA\Property(property="prenom", type="string", example="Jean"),
 *             @OA\Property(property="email", type="string", format="email", example="jean.dupont@example.com"),
 *             @OA\Property(property="password", type="string", example="yourpassword"),
 *             @OA\Property(property="adresse", type="string", example="123 rue de la Paix"),
 *             @OA\Property(property="niveauEtude", type="string", example="Master en informatique"),
 *             @OA\Property(property="dateNaissance", type="string", format="date", example="1990-01-01")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Admin inscrit avec succès"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Données invalides"
 *     )
 * )
 */

//ladmin
#[Route('/inscrire-admin', name: 'inscrire_admin', methods: ['POST'])]
public function ajouterUtilisateurAdmin(EntityManagerInterface $em, Request $request, UserPasswordHasherInterface $passwordHasher, ValidatorInterface $validator): JsonResponse
{
    $data = json_decode($request->getContent(), true);

    $constraints = new Assert\Collection([
        'nom' => [
            new Assert\NotBlank(),
            new Assert\Type(['type' => 'string']),
            new Assert\Regex('/^[a-zA-Z]+$/'),
            new Assert\Length(['min' => 2, 'max' => 20]),
        ],
        'prenom' => [
            new Assert\NotBlank(),
            new Assert\Type(['type' => 'string']),
            new Assert\Regex('/^[a-zA-Z]+$/'),
            new Assert\Length(['min' => 2, 'max' => 20]),
        ],  
        'email' => [
            new Assert\NotBlank(),
            new Assert\Email(),
            new Assert\Regex([
                'pattern' => '/^[a-zA-Z]{3,}@/',
                'message' => 'L\'email doit contenir au moins 3 lettres avant le @.'
            ])
        ],
        'password' => [
            new Assert\NotBlank(),
            new Assert\Length(['min' => 6, 'max' => 9]),
            new Assert\Regex([
                'pattern' => '/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,9}$/',
                'message' => 'Le mot de passe doit contenir au moins 6 caractères, au moins une lettre, un chiffre et un caractère spécial.'
            ])
        ],
        'numeroTelephone' => [new Assert\NotBlank()],
     
    ]);

    $violations = $validator->validate($data, $constraints);
    if (count($violations) > 0) {
        return $this->json(['errors' => (string) $violations], JsonResponse::HTTP_BAD_REQUEST);
    }

   

    $user = new User();
    $user->setNom($data['nom']);
    $user->setPrenom($data['prenom']);
    $user->setEmail($data['email']);
    $user->setPassword($passwordHasher->hashPassword($user, $data['password']));
    $user->setNumeroTelephone($data['numeroTelephone']);
    $user->setRoles(['ROLE_ADMIN']); 

    

    $em->persist($user);
    $em->flush();

    $responseData = [
        'email' => $user->getEmail(),
    'nom' => $user->getNom(),
    'prenom' => $user->getPrenom(),
    'adresse' => $user->getAdresse(),
        'message' => 'Inscription du Admin réussie',
    ];

    return $this->json($responseData, JsonResponse::HTTP_CREATED);
}



}
