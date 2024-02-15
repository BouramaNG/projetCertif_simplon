<?php

namespace App\Controller;

use App\Entity\Talibe;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
#[Route('/api', name: 'api_')]
class TesteController extends AbstractController
{
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
