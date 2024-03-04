<?php

namespace App\Tests\Functional;

use App\Entity\Role;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AjoutRolesTest extends WebTestCase
{
    public function testAjoutRoles()
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get('doctrine')->getManager();

        $roles = ['DONATEUR', 'ADMIN', 'DAHRA','MARRAINE'];

        foreach ($roles as $roleName) {
            $role = new Role();
            $role->setNomRole($roleName);
            $entityManager->persist($role);
        }

        $entityManager->flush();

        // Vérification que les rôles ont été ajoutés
        $rolesRepository = $entityManager->getRepository(Role::class);
        $donateurRole = $rolesRepository->findOneBy(['nomRole' => 'MARRAINE']);
        $this->assertNotNull($donateurRole);

        $donateurRole = $rolesRepository->findOneBy(['nomRole' => 'DONATEUR']);
        $this->assertNotNull($donateurRole);

        $adminRole = $rolesRepository->findOneBy(['nomRole' => 'ADMIN']);
        $this->assertNotNull($adminRole);

        $dahraRole = $rolesRepository->findOneBy(['nomRole' => 'DAHRA']);
        $this->assertNotNull($dahraRole);
    }
}
