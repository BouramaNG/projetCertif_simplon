<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Marraine;
use OpenApi\Annotations as OA;
use Doctrine\ORM\EntityManagerInterface;
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
   
    public function register(EntityManagerInterface $em, Request $request, UserPasswordHasherInterface $passwordHasher, ValidatorInterface $validator): JsonResponse
{
    $data = json_decode($request->getContent(), true);

    $constraints = new Assert\Collection([
        'nom' => [new Assert\NotBlank(), new Assert\Length(['min' => 4])],
        'prenom' => [new Assert\NotBlank(), new Assert\Length(['min' => 4])],
        'email' => [new Assert\NotBlank(), new Assert\Email()],
        'password' => [new Assert\NotBlank()],
        'adresse' => [new Assert\NotBlank()],
        'numeroTelephone' => [new Assert\NotBlank(), new Assert\Length(['min' => 5])],
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
    $user->setAdresse($data['adresse']);
    $user->setNumeroTelephone($data['numeroTelephone']);
$user->setRoles(['ROLE_DONATEUR']);


    $em->persist($user);
    $em->flush();

    $responseData = [
        'nom' => $user->getNom(),
        'prenom' => $user->getPrenom(),
        'adresse' => $user->getAdresse(),
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
 *     ),
 *     security={
 *         {"bearerAuth": {}}
 *     }
 * )
 */

#[Route('/modifier-donateur/{id}', name: 'modifier_donateur', methods: ['PUT'])]
public function updateDonateur(int $id, Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
{
    $data = json_decode($request->getContent(), true);
    $constraints = new Assert\Collection([
        'nom' => [new Assert\NotBlank(), new Assert\Length(['min' => 4])],
        'prenom' => [new Assert\NotBlank(), new Assert\Length(['min' => 4])],
        'email' => [new Assert\NotBlank(), new Assert\Email()],
        'adresse' => [new Assert\NotBlank()],
        'numeroTelephone' => [new Assert\NotBlank(), new Assert\Length(['min' => 5])],
        'password' => [new Assert\NotBlank()],  // Ajoutez la contrainte directement au tableau associé au champ 'password'
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
 *             @OA\Property(property="niveauEtude", type="string", example="Master en informatique"),
 *             @OA\Property(property="dateNaissance", type="string", format="date", example="1990-01-01")
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
   
public function devenirMarraine(EntityManagerInterface $em, Request $request, UserPasswordHasherInterface $passwordHasher, ValidatorInterface $validator): JsonResponse
{
$data = json_decode($request->getContent(), true);

$constraints = new Assert\Collection([
    'nom' => [new Assert\NotBlank(), new Assert\Length(['min' => 4])],
    'prenom' => [new Assert\NotBlank(), new Assert\Length(['min' => 4])],
    'email' => [new Assert\NotBlank(), new Assert\Email()],
    'password' => [new Assert\NotBlank()],
    'adresse' => [new Assert\NotBlank()],
    'numeroTelephone' => [new Assert\NotBlank(), new Assert\Length(['min' => 5])],
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
$user->setAdresse($data['adresse']);
$user->setNumeroTelephone($data['numeroTelephone']);
$user->setRoles(['ROLE_MARRAINE']);


$em->persist($user);
$em->flush();

$responseData = [
    'nom' => $user->getNom(),
    'prenom' => $user->getPrenom(),
    'adresse' => $user->getAdresse(),
    'message' => 'Inscription du Marraine réussie',
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
 *     ),
 *     security={
 *         {"bearerAuth": {}}
 *     }
 * )
 */

#[Route('/modifier-marraine/{id}', name: 'modifier_marraine', methods: ['PUT'])]
public function modifierMarraine(int $id, Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
{
    $data = json_decode($request->getContent(), true);

    $constraints = new Assert\Collection([
        'nom' => [new Assert\NotBlank(), new Assert\Length(['min' => 4])],
        'prenom' => [new Assert\NotBlank(), new Assert\Length(['min' => 4])],
        'email' => [new Assert\NotBlank(), new Assert\Email()],
        'adresse' => [new Assert\NotBlank()],
        'numeroTelephone' => [new Assert\NotBlank(), new Assert\Length(['min' => 5])],
        'password' => [new Assert\NotBlank()],
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
        'nom' => [new Assert\NotBlank(), new Assert\Length(['min' => 2]), new Assert\Regex('/^[a-zA-Z]+$/')],
        'prenom' => [new Assert\NotBlank(), new Assert\Length(['min' => 4]), new Assert\Regex('/^[a-zA-Z]+$/')],
        'email' => [new Assert\NotBlank()],
        'password' => [new Assert\NotBlank()],
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
        'nom' => $user->getNom(),
        'prenom' => $user->getPrenom(),
        'adresse' => $user->getAdresse(),
        'message' => 'Inscription du Admin réussie',
    ];

    return $this->json($responseData, JsonResponse::HTTP_CREATED);
}



}
