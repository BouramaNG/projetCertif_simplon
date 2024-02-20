<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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

use Symfony\Component\HttpFoundation\File\File;

use Symfony\Component\HttpFoundation\JsonResponse;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


   
/**
 * @OA\Post(
 *     path="/api/register/dahra",
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
class DaaraController extends AbstractController
{
 
#[Route('/register/dahra', name: 'register_dahra', methods: ['POST'])]
 
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

    $responseData = $serializer->serialize($dahra, 'json', ['groups' => 'dahra']);
    return new JsonResponse($responseData, Response::HTTP_CREATED, [], true);
}


/**
 * @OA\Put(
 *     path="/archiver-talibe/{id}",
 *     summary="Archive un talibé",
 *     tags={"Talibes"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         description="ID du talibé à archiver",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Talibé archivé avec succès",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Talibé archivé avec succès")
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Accès refusé"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Talibé non trouvé"
 *     )
 * )
 */
#[Route('/archiver-talibe/{id}', name: 'archiver_talibe', methods: ['PUT'])]
public function archiverTalibe(EntityManagerInterface $em, $id, Security $security): JsonResponse
{
    $user = $security->getUser();
    if (!$user || !in_array('ROLE_DAHRA', $user->getRoles())) {
        return new JsonResponse(['message' => 'Accès refusé'], JsonResponse::HTTP_FORBIDDEN);
    }

    $talibe = $em->getRepository(Talibe::class)->find($id);

    if (!$talibe) {
        return new JsonResponse(['message' => 'Talibe non trouvé'], JsonResponse::HTTP_NOT_FOUND);
    }

    $talibe->setArchived(true);

    $em->flush();

    return new JsonResponse(['message' => 'Talibe archivé avec succès'], JsonResponse::HTTP_OK);
}

}
