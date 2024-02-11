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
 * Ajouter un nouveau Talibe à un Dahra.
 * 
 * @OA\Post(
 *     path="/api/inscrire/add-talibe",
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

 #[Route('/api', name: 'api_')]
class TalibeController extends AbstractController
{
    #[Route('/inscrire/add-talibe', name: 'inscrire_add_talibe', methods: ['POST'])]
    public function addTalibeToDahra(Request $request, EntityManagerInterface $entityManager, Security $security, ValidatorInterface $validator,FileUploader $fileUploader): Response
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
        $talibe->setNom($request->request->get('nom'));
        $talibe->setPrenom($request->request->get('prenom'));
        $talibe->setAge($request->request->get('age'));
        $talibe->setAdresse($request->request->get('adresse'));
        $talibe->setSituation($request->request->get('situation'));
        $talibe->setDescription($request->request->get('description'));
        $talibe->setImage($request->request->get('image') ?? null);
    
        // $dateArriveTalibe = \DateTime::createFromFormat('Y-m-d', $request->request->get('datearrivetalibe'));
        $dateArriveTalibe = \DateTimeImmutable::createFromFormat('Y-m-d', $request->request->get('datearrivetalibe'));
        $talibe->setDateArriveTalibe($dateArriveTalibe);
        

    
        $talibe->setPresenceTalibe($request->request->get('presencetalibe') ?? 'present');
    
        $talibe->setDahra($dahra);
        $violations = $validator->validate($talibe);
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
        if ($request->files->has('imageFile')) {
            $imageFile = $request->files->get('imageFile');
            if ($imageFile) {
              
                $uploadedFilePath = $fileUploader->upload($imageFile);
                
            
                $talibe->setImageFilename($uploadedFilePath);
            }
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
}
