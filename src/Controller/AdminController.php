<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Dahra;
use App\Entity\Talibe;
use App\Entity\FaireDon;
use App\Entity\Parrainage;
use Psr\Log\LoggerInterface;
use OpenApi\Annotations as OA;
use Symfony\Component\Mime\Email;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
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
 *     path="/api/ajouter-dahra",
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
class AdminController extends AbstractController
{
    

#[Route('/ajouter-dahra', name: 'ajouter_dahra', methods: ['POST'])]

public function ajouterDahra(Request $request,Security $security, UserPasswordHasherInterface $passwordHasher, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
    {

        $user = $security->getUser();
    if (!$user || !in_array('ROLE_ADMIN', $user->getRoles())) {
        return new JsonResponse(['message' => 'Accès refusé'], JsonResponse::HTTP_FORBIDDEN);
    }
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
 *     summary="Mettre à jour une Dahra",
 *     description="Permet à un administrateur de mettre à jour les informations d'une Dahra",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"nom", "nomOuztas", "adresse", "region", "numeroTelephoneOuztas"},
 *             @OA\Property(property="nom", type="string", example="Nom de la Dahra"),
 *             @OA\Property(property="nomOuztas", type="string", example="Nom Ouztas"),
 *             @OA\Property(property="adresse", type="string", example="Adresse"),
 *             @OA\Property(property="region", type="string", example="Region"),
 *             @OA\Property(property="numeroTelephoneOuztas", type="string", example="Numéro de téléphone")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Dahra mise à jour avec succès"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Données invalides"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Dahra non trouvée"
 *     )
 * )
 */



#[Route('/modifier-dahra/{id}', name: 'modifier-dahra/{id}', methods: ['POST'])]
public function modifierDahra(int $id, Request $request, UserPasswordHasherInterface $passwordHasher, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator, Security $security): JsonResponse
{
    $user = $security->getUser();

    if (!$user || !in_array('ROLE_ADMIN', $user->getRoles())) {
        return new JsonResponse(['message' => 'Accès refusé'], JsonResponse::HTTP_FORBIDDEN);
    }

    $dahra = $entityManager->getRepository(Dahra::class)->find($id);

    if (!$dahra) {
        return new JsonResponse(['message' => 'Dahra non trouvé'], JsonResponse::HTTP_NOT_FOUND);
    }

    $data = json_decode($request->getContent(), true);

    $constraints = new Assert\Collection([
        'nom' => [new Assert\NotBlank(), new Assert\Type(['type' => 'string'])],
        'nomOuztas' => [new Assert\NotBlank(), new Assert\Type(['type' => 'string'])],
        'adresse' => [new Assert\NotBlank(), new Assert\Type(['type' => 'string'])],
        'region' => [new Assert\NotBlank(), new Assert\Type(['type' => 'string'])],
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

    $dahra->setNom($data['nom']);
    $dahra->setNomOuztas($data['nomOuztas']);
    $dahra->setAdresse($data['adresse']);
    $dahra->setRegion($data['region']);
    $dahra->setNumeroTelephoneOuztas($data['numeroTelephoneOuztas']);
    $dahra->setNombreTalibe($data['nombreTalibe']);

    $entityManager->flush();

    $responseData = $serializer->serialize($dahra, 'json');
    return new JsonResponse($responseData, JsonResponse::HTTP_OK, [], true);
}

/**
 * @OA\Delete(
 *     path="/api/supprimer-dahra/{id}",
 *     summary="Supprimer une Dahra",
 *     description="Permet à un administrateur de supprimer une Dahra existante.",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID de la Dahra à supprimer",
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Dahra supprimé avec succès"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Accès refusé"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Dahra non trouvée"
 *     ),
 *     security={
 *         {"bearerAuth": {}}
 *     }
 * )
 */


#[Route('/supprimer-dahra/{id}', name: 'supprimer_dahra', methods: ['POST'])]
public function supprimerDahra(EntityManagerInterface $em, $id,Security $security): JsonResponse
{
    $user = $security->getUser();
    if (!$user || !in_array('ROLE_ADMIN', $user->getRoles())) {
        return new JsonResponse(['message' => 'Accès refusé'], JsonResponse::HTTP_FORBIDDEN);
    }
    $dahra = $em->getRepository(Dahra::class)->find($id);
    
    if (!$dahra) {
        return $this->json(['message' => 'Dahra non trouvé'], JsonResponse::HTTP_NOT_FOUND);
    }

    $em->remove($dahra);
    $em->flush();

    return $this->json(['message' => 'Dahra supprimé avec succès'], JsonResponse::HTTP_OK);
}


/**
 * @OA\Post(
 *     path="/api/ajouter-talibe",
 *     summary="Ajouter un Talibe",
 *     description="Permet à un administrateur d'ajouter un nouveau Talibe.",
 *     @OA\RequestBody(
 *         required=true,
 *         description="Données du Talibe à ajouter",
 *         @OA\JsonContent(
 *             required={"nom", "prenom", "adresse", "age", "situation", "description", "image", "dahra_id"},
 *             @OA\Property(property="nom", type="string", example="Nom du Talibe"),
 *             @OA\Property(property="prenom", type="string", example="Prénom du Talibe"),
 *             @OA\Property(property="adresse", type="string", example="Adresse du Talibe"),
 *             @OA\Property(property="age", type="integer", example=20),
 *             @OA\Property(property="situation", type="string", example="Situation du Talibe"),
 *             @OA\Property(property="description", type="string", example="Description du Talibe"),
 *             @OA\Property(property="image", type="string", example="Lien vers l'image du Talibe"),
 *             @OA\Property(property="dahra_id", type="integer", example=1)
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Talibe ajouté avec succès"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Données invalides ou ID du Dahra manquant"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Accès refusé"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Dahra non trouvé"
 *     ),
 *     security={
 *         {"bearerAuth": {}}
 *     }
 * )
 */
#[Route('/ajouter-talibe', name: 'ajouter_talibe', methods: ['POST'])]
public function ajouterTalibe(Request $request, EntityManagerInterface $em, ValidatorInterface $validator, SerializerInterface $serializer,Security $security): JsonResponse
{
    
    $user = $security->getUser();
    if (!$user || !in_array('ROLE_ADMIN', $user->getRoles())) {
        return new JsonResponse(['message' => 'Accès refusé'], JsonResponse::HTTP_FORBIDDEN);
    }
    $data = json_decode($request->getContent(), true);
    $constraints = new Assert\Collection([
        'nom' => [new Assert\NotBlank(), new Assert\Length(['min' => 4])],
        'prenom' => [new Assert\NotBlank(), new Assert\Length(['min' => 4])],
        'adresse' => [new Assert\NotBlank()],
        'age' => [new Assert\NotBlank()],
        'situation' => [new Assert\NotBlank()],
        'description' => [new Assert\NotBlank()],
        'image' => [new Assert\NotBlank()],
        
    ]);

    $violations = $validator->validate($data, $constraints);
    if (count($violations) > 0) {
        return $this->json(['errors' => (string) $violations], JsonResponse::HTTP_BAD_REQUEST);
    }
   
    if (!isset($data['dahra_id']) || empty($data['dahra_id'])) {
        return new JsonResponse(['message' => 'ID du Dahra manquant'], JsonResponse::HTTP_BAD_REQUEST);
    }

    $dahra = $em->getRepository(Dahra::class)->find($data['dahra_id']);
    if (!$dahra) {
        return new JsonResponse(['message' => 'Dahra non trouvé'], JsonResponse::HTTP_NOT_FOUND);
    }

    $talibe = $serializer->deserialize($request->getContent(), Talibe::class, 'json');

 
    $talibe->setDahra($dahra);

    $errors = $validator->validate($talibe);
    if (count($errors) > 0) {
        return new JsonResponse((string) $errors, JsonResponse::HTTP_BAD_REQUEST);
    }

    $em->persist($talibe);
    $em->flush();

    return new JsonResponse(['message' => 'Talibe ajouté avec succès'], JsonResponse::HTTP_CREATED);
}




#[Route('/supprimer-talibe/{id}', name: 'supprimer_talibe', methods: ['DELETE'])]
public function supprimerTalibe(EntityManagerInterface $em, $id,Security $security): JsonResponse
{
    
    $user = $security->getUser();
    if (!$user || !in_array('ROLE_ADMIN', $user->getRoles())) {
        return new JsonResponse(['message' => 'Accès refusé'], JsonResponse::HTTP_FORBIDDEN);
    }
    $talibe = $em->getRepository(Talibe::class)->find($id);

    if (!$talibe) {
        return new JsonResponse(['message' => 'Talibe non trouvé'], JsonResponse::HTTP_NOT_FOUND);
    }

    $em->remove($talibe);
    $em->flush();

    return new JsonResponse(['message' => 'Talibe supprimé avec succès'], JsonResponse::HTTP_OK);
}

/**
 * @OA\Put(
 *     path="/api/modifier-talibe/{id}",
 *     summary="Modifier un Talibe",
 *     description="Permet à un administrateur de modifier les informations d'un Talibe existant.",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID du Talibe à modifier",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         description="Données du Talibe à modifier",
 *         @OA\JsonContent(
 *             @OA\Property(property="nom", type="string", example="Nouveau Nom"),
 *             @OA\Property(property="prenom", type="string", example="Nouveau Prénom"),
 *             @OA\Property(property="adresse", type="string", example="Nouvelle Adresse"),
 *             @OA\Property(property="age", type="integer", example=25),
 *             @OA\Property(property="situation", type="string", example="Nouvelle Situation"),
 *             @OA\Property(property="description", type="string", example="Nouvelle Description"),
 *             @OA\Property(property="image", type="string", example="Lien vers la nouvelle image"),
 *             @OA\Property(property="dahra_id", type="integer", example=2)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Talibe modifié avec succès"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Données invalides"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Accès refusé"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Talibe ou Dahra non trouvé"
 *     ),
 *     security={
 *         {"bearerAuth": {}}
 *     }
 * )
 */
#[Route('/modifier-talibe/{id}', name: 'modifier_talibe', methods: ['PUT'])]
public function modifierTalibe(Request $request, EntityManagerInterface $em, ValidatorInterface $validator, SerializerInterface $serializer, $id,Security $security): JsonResponse
{
  
    $user = $security->getUser();
    if (!$user || !in_array('ROLE_ADMIN', $user->getRoles())) {
        return new JsonResponse(['message' => 'Accès refusé'], JsonResponse::HTTP_FORBIDDEN);
    }
    $talibe = $em->getRepository(Talibe::class)->find($id);

    if (!$talibe) {
        return new JsonResponse(['message' => 'Talibe non trouvé'], JsonResponse::HTTP_NOT_FOUND);
    }

    $data = json_decode($request->getContent(), true);

    
    if (isset($data['dahra_id']) && $data['dahra_id'] !== $talibe->getDahra()?->getId()) {
        $dahra = $em->getRepository(Dahra::class)->find($data['dahra_id']);
        if (!$dahra) {
            return new JsonResponse(['message' => 'Dahra non trouvé'], JsonResponse::HTTP_NOT_FOUND);
        }
        $talibe->setDahra($dahra);
    }

    $serializer->deserialize($request->getContent(), Talibe::class, 'json', ['object_to_populate' => $talibe]);

    $errors = $validator->validate($talibe);
    if (count($errors) > 0) {
        return new JsonResponse((string) $errors, JsonResponse::HTTP_BAD_REQUEST);
    }

    $em->flush();

    return new JsonResponse(['message' => 'Talibe modifié avec succès'], JsonResponse::HTTP_OK);
}

/**
 * @OA\Get(
 *     path="/api/lister-talibe",
 *     summary="Lister tous les Talibes",
 *     description="Récupère une liste de tous les Talibes enregistrés dans le système.",
 *     @OA\Response(
 *         response=200,
 *         description="Liste des Talibes récupérée avec succès",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="prenom", type="string", example="Prenom1"),
 *                 @OA\Property(property="nom", type="string", example="Nom1"),
 *                 @OA\Property(property="age", type="integer", example=20),
 *                 @OA\Property(property="adresse", type="string", example="Adresse1"),
 *                 @OA\Property(property="situation", type="string", example="Situation1"),
 *                 @OA\Property(property="description", type="string", example="Description1"),
 *                 @OA\Property(property="image", type="string", example="lien-image"),
 *                 @OA\Property(property="datearrivetalibe", type="string", format="date-time", example="2024-01-20T14:00:00Z"),
 *                 @OA\Property(property="dahraNom", type="string", example="Nom Dahra")
 *             )
 *         )
 *     ),
 *     security={
 *         {"bearerAuth": {}}
 *     }
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
 *     path="/api/lister-dahra",
 *     summary="Lister tous les Dahras",
 *     description="Récupère une liste de tous les Dahras enregistrés dans le système.",
 *     @OA\Response(
 *         response=200,
 *         description="Liste des Dahras récupérée avec succès",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="nom", type="string", example="Dahra Al-Azhar"),
 *                 @OA\Property(property="adresse", type="string", example="123 Rue Principale"),
 *                 @OA\Property(property="region", type="string", example="Dakar"),
 *                 @OA\Property(property="nombreTalibe", type="integer", example=50),
 *                 @OA\Property(property="nomOuztas", type="string", example="Oustaz Ali"),
 *                 @OA\Property(property="numeroTelephoneOuztas", type="string", example="+221123456789"),
 *             )
 *         )
 *     ),
 *     security={
 *         {"bearerAuth": {}}
 *     }
 * )
 */


#[Route('/lister-dahra', name: 'lister_dahra', methods: ['GET'])]
public function listerDahra(EntityManagerInterface $em): JsonResponse
{
    $dahras = $em->getRepository(Dahra::class)->findAll();

    $data = [];
    foreach ($dahras as $dahra) {
        $data[] = [
            'id' => $dahra->getId(),
            'nom' => $dahra->getNom(),
            'adresse' => $dahra->getAdresse(),
            'region' => $dahra->getRegion(),
            'nombreTalibe' => $dahra->getNombreTalibe(),
            'nomOuztas' => $dahra->getNomOuztas(),
            'numeroTelephoneOuztas' => $dahra->getNumeroTelephoneOuztas(),
           
        ];
    }

    return new JsonResponse($data, JsonResponse::HTTP_OK);
}

/**
 * @OA\Put(
 *     path="/api/modifier-statut-parrainage/{id}",
 *     summary="Modifier le statut d'un parrainage",
 *     description="Permet à un administrateur de modifier le statut d'un parrainage existant.",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID du parrainage",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"status"},
 *             @OA\Property(property="status", type="string", example="valide", description="Nouveau statut du parrainage (valide ou rejeter)")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Statut du parrainage mis à jour",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Statut du parrainage mis à jour")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Requête invalide ou statut non modifiable"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Parrainage non trouvé"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Accès refusé"
 *     ),
 *     security={
 *         {"bearerAuth": {}}
 *     }
 * )
 */

#[Route('/modifier-statut-parrainage/{id}', name: 'modifier_statut_parrainage', methods: ['PUT'])]
public function modifierStatutParrainage(Request $request, EntityManagerInterface $em, $id,Security $security): JsonResponse
{
    $user = $security->getUser();
    if (!$user || !in_array('ROLE_ADMIN', $user->getRoles())) {
        return new JsonResponse(['message' => 'Accès refusé'], JsonResponse::HTTP_FORBIDDEN);
    }
    $parrainage = $em->getRepository(Parrainage::class)->find($id);

    if (!$parrainage) {
        return new JsonResponse(['message' => 'Parrainage non trouvé'], JsonResponse::HTTP_NOT_FOUND);
    }

    
    if ($parrainage->getStatus() !== 'en cours') {
        return new JsonResponse(['message' => 'Le statut du parrainage n\'est pas modifiable'], JsonResponse::HTTP_BAD_REQUEST);
    }

   
    $data = json_decode($request->getContent(), true);
    $nouveauStatut = $data['status'] ?? null;

    
    if (!in_array($nouveauStatut, ['valide', 'rejeter'])) {
        return new JsonResponse(['message' => 'Statut invalide'], JsonResponse::HTTP_BAD_REQUEST);
    }

   
    $parrainage->setStatus($nouveauStatut);
    $em->flush();

    return new JsonResponse(['message' => 'Statut du parrainage mis à jour'], JsonResponse::HTTP_OK);
}
/**
 * @OA\Post(
 *     path="/api/modifier-statut-don/{id}",
 *     summary="Modifier le statut d'un don",
 *     description="Permet à un administrateur de modifier le statut d'un don existant.",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID du don",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"status"},
 *             @OA\Property(property="status", type="string", example="valide", description="Nouveau statut du don")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Statut du don modifié avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Statut du don modifié avec succès")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Requête invalide ou erreur dans la modification du statut"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Don non trouvé"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Accès refusé"
 *     ),
 *     security={
 *         {"bearerAuth": {}}
 *     }
 * )
 */

#[Route('/modifier-statut-don/{id}', name: 'modifier_statut_don', methods: ['POST'])]
public function modifierStatutDon(int $id, Request $request, EntityManagerInterface $em, Security $security): JsonResponse
{
   
    $user = $security->getUser();
    if (!$user || !in_array('ROLE_ADMIN', $user->getRoles())) {
        return new JsonResponse(['message' => 'Accès refusé'], JsonResponse::HTTP_FORBIDDEN);
    }

    $data = json_decode($request->getContent(), true);
    $nouveauStatut = $data['status'] ?? null;

    $faireDon = $em->getRepository(FaireDon::class)->find($id);
    if (!$faireDon) {
        return new JsonResponse(['message' => 'Don non trouvé'], JsonResponse::HTTP_NOT_FOUND);
    }

    $faireDon->setStatus($nouveauStatut);
    
    $em->persist($faireDon);
    $em->flush();

    return new JsonResponse(['message' => 'Statut du don modifié avec succès'], JsonResponse::HTTP_OK);
}

/**
 * @OA\Post(
 *     path="/api/admin/assigner-role",
 *     summary="Assigner ou retirer un rôle à un utilisateur",
 *     description="Permet à un administrateur d'assigner ou de retirer un rôle à un utilisateur spécifique.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"user_id", "role"},
 *             @OA\Property(property="user_id", type="integer", example=123, description="ID de l'utilisateur"),
 *             @OA\Property(property="role", type="string", example="ROLE_USER", description="Rôle à assigner ou retirer")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Rôle modifié avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Rôle modifié avec succès")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Données manquantes ou requête invalide"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Utilisateur non trouvé"
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Accès refusé"
 *     ),
 *     security={
 *         {"bearerAuth": {}}
 *     }
 * )
 */

#[Route('/admin/assigner-role', name: 'admin_assigne_role', methods: ['POST'])]
public function assignRole(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager, Security $security): JsonResponse {
   
    
    $currentUser = $security->getUser();
    if (!$currentUser || !in_array('ROLE_ADMIN', $currentUser->getRoles())) {
        return new JsonResponse(['message' => 'Accès refusé'], JsonResponse::HTTP_FORBIDDEN);
    }

    $data = json_decode($request->getContent(), true);
    $userId = $data['user_id'] ?? null;
    $role = $data['role'] ?? null;

    if (!$userId || !$role) {
        return new JsonResponse(['message' => 'Données manquantes'], JsonResponse::HTTP_BAD_REQUEST);
    }

    $userToModify = $userRepository->find($userId);
    if (!$userToModify) {
        return new JsonResponse(['message' => 'Utilisateur non trouvé'], JsonResponse::HTTP_NOT_FOUND);
    }

    $userRoles = $userToModify->getRoles();
    if (in_array($role, $userRoles)) {
        
        $userRoles = array_diff($userRoles, [$role]);
    } else {
        
        $userRoles[] = $role;
    }
    $userToModify->setRoles(array_values($userRoles)); 

    $entityManager->persist($userToModify); 
    $entityManager->flush();

    return new JsonResponse(['message' => 'Rôle modifié avec succès']);
}
/**
 * @OA\Post(
 *     path="/admin/activate/dahra/{id}",
 *     summary="Activer un Dahra par l'administrateur",
 *     description="Permet à l'administrateur d'activer un Dahra en spécifiant son ID.",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID du Dahra à activer",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Dahra activé avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Dahra activé avec succès")
 *         )
 *     ),
 *     @OA\Response(response=404, description="Utilisateur non trouvé"),
 *     security={{"bearerAuth": {}}}
 * )
 */

#[Route('/admin/activate/dahra/{id}', name: 'admin_activate_dahra', methods: ['POST'])]
public function activateDahra(int $id, EntityManagerInterface $entityManager): JsonResponse
{
    $user = $entityManager->getRepository(User::class)->find($id);

    if (!$user) {
        return new JsonResponse(['error' => 'Utilisateur non trouvé'], Response::HTTP_NOT_FOUND);
    }

    $user->setIsActive(true);
    $entityManager->flush();

    return new JsonResponse(['message' => 'Dahra activé avec succès']);
}
/**
 * @OA\Post(
 *     path="/demande-reset-password",
 *     summary="Demander la réinitialisation de mot de passe",
 *     description="Permet à un utilisateur de demander la réinitialisation de son mot de passe en fournissant son adresse e-mail.",
 *     @OA\RequestBody(
 *         required=true,
 *         description="Données nécessaires pour la demande de réinitialisation",
 *         @OA\JsonContent(
 *             required={"email"},
 *             @OA\Property(property="email", type="string", format="email", example="utilisateur@example.com")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Email de réinitialisation envoyé",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Email de réinitialisation envoyé")
 *         )
 *     ),
 *     @OA\Response(response=404, description="Utilisateur non trouvé"),
 *     @OA\Response(response=500, description="Erreur lors de l'envoi de l'email"),
 * )
 */

#[Route('/demande-reset-password', name: 'demande_reset_password', methods: ['POST'])]
public function demandeResetPassword(EntityManagerInterface $em, Request $request, MailerInterface $mailer): JsonResponse {
    $data = json_decode($request->getContent(), true);
    $user = $em->getRepository(User::class)->findOneBy(['email' => $data['email']]);
    
    if (!$user) {
        return $this->json(['message' => 'Utilisateur non trouvé'], JsonResponse::HTTP_NOT_FOUND);
    }

    $resetToken = bin2hex(random_bytes(32));
    $user->setResetToken($resetToken);
    $em->persist($user);
    $em->flush();

    $resetLink = 'http://127.0.0.1:8000/reset-password?token=' . $resetToken;

    $email = (new Email())
        ->from('ngombourama@gmail.com')
        ->to($user->getEmail())
        ->subject('Réinitialisation de votre mot de passe')
        ->html('<p>Pour réinitialiser votre mot de passe, veuillez cliquer sur ce lien : <a href="' . $resetLink . '">Réinitialiser mon mot de passe</a></p>');
    
    try {
        $mailer->send($email);
        return $this->json(['message' => 'Email de réinitialisation envoyé'], JsonResponse::HTTP_OK);
    } catch (\Exception $e) {
        return $this->json(['message' => 'Erreur lors de l\'envoi de l\'email: ' . $e->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    }
}
/**
 * @OA\Get(
 *     path="/reset-password",
 *     summary="Afficher le formulaire de réinitialisation de mot de passe",
 *     description="Affiche le formulaire de réinitialisation de mot de passe en fonction du jeton fourni dans l'URL.",
 *     @OA\Parameter(
 *         name="token",
 *         in="query",
 *         required=true,
 *         description="Jeton de réinitialisation de mot de passe",
 *         @OA\Schema(type="string", example="your_reset_token_here")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Affiche le formulaire de réinitialisation de mot de passe",
 *         @OA\JsonContent(
 *             @OA\Property(property="resetToken", type="string", example="your_reset_token_here")
 *         )
 *     ),
 *     @OA\Response(response=404, description="Jeton de réinitialisation non fourni"),
 * )
 */
#[Route('/reset-password', name: 'show_reset_password_form', methods: ['GET'])]
public function showResetPasswordForm(Request $request): Response {
    $token = $request->query->get('token');
    return $this->render('reset_password_form.html.twig', [
        'resetToken' => $token,
    ]);
}
/**
 * @OA\Post(
 *     path="/reset-password",
 *     summary="Réinitialiser le mot de passe",
 *     description="Réinitialise le mot de passe de l'utilisateur en utilisant le jeton de réinitialisation fourni.",
 *     @OA\RequestBody(
 *         required=true,
 *         description="Données nécessaires pour réinitialiser le mot de passe",
 *         @OA\JsonContent(
 *             required={"token", "password"},
 *             @OA\Property(property="token", type="string", example="your_reset_token_here"),
 *             @OA\Property(property="password", type="string", example="new_password_here")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Mot de passe réinitialisé avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Mot de passe réinitialisé avec succès")
 *         )
 *     ),
 *     @OA\Response(response=400, description="Token invalide ou données manquantes"),
 * )
 */

#[Route('/reset-password', name: 'reset_password', methods: ['POST'])]
public function resetPassword(EntityManagerInterface $em, Request $request, UserPasswordHasherInterface $passwordHasher): JsonResponse {
    $data = json_decode($request->getContent(), true);
    $user = $em->getRepository(User::class)->findOneBy(['resetToken' => $data['token']]);

    if (!$user) {
        return $this->json(['message' => 'Token invalide'], JsonResponse::HTTP_BAD_REQUEST);
    }

    $user->setPassword($passwordHasher->hashPassword($user, $data['password']));
    $user->setResetToken(null);
    $em->persist($user);
    $em->flush();

    return $this->json(['message' => 'Mot de passe réinitialisé avec succès'], JsonResponse::HTTP_OK);
}



}
