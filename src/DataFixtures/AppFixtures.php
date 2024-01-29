<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use OpenApi\Annotations as OA;
class AppFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager)
    {
        // Création d'un utilisateur admin
        $admin = new User();
        $admin->setEmail('soly@gmail.com');
        $admin->setNom('fatou');
        $admin->setPrenom('soly');
        $admin->setAdresse('dakar');
        $admin->setNumeroTelephone('783718472');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'Adminpass1@'));
        $admin->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);

        // Création d'un utilisateur donateur
        $donateur = new User();
        $donateur->setEmail('donateur@gmail.com');
        $donateur->setNom('fatou');
        $donateur->setPrenom('diop');
        $donateur->setAdresse('dakar');
        $donateur->setNumeroTelephone('783718472');
        $donateur->setPassword($this->passwordHasher->hashPassword($donateur, 'donatepass'));
        $donateur->setRoles(['ROLE_DONATEUR']);
        $manager->persist($donateur);

        $marraine = new User();
        $marraine->setEmail('marraine@gmail.com');
        $marraine->setNom('marraine');
        $marraine->setPrenom('diop');
        $marraine->setAdresse('dakar');
        $marraine->setNumeroTelephone('783718472');
        $marraine->setPassword($this->passwordHasher->hashPassword($marraine, 'marrainepass'));
        $marraine->setRoles(['ROLE_MARRAINE']);
        $manager->persist($marraine);

        $dahra = new User();
        $dahra->setEmail('dahra@gmail.com');
        $dahra->setNom('dahra');
        $dahra->setPrenom('diop');
        $dahra->setAdresse('dakar');
        $dahra->setNumeroTelephone('783718472');
        $dahra->setPassword($this->passwordHasher->hashPassword($dahra, 'dahrapass'));
        $dahra->setRoles(['ROLE_DAHRA']);
        $manager->persist($dahra);

        // si on a un autre role on le met ici

        $manager->flush();
    }
}
