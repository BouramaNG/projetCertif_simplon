<?php

namespace App\Service;

use App\Entity\BlackListedTocken;
use Doctrine\ORM\EntityManagerInterface;

class TokenBlacklistService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function addToBlacklist(string $token): void
    {
        if (empty($token)) {
            throw new \InvalidArgumentException('Le token ne peut pas être vide.');
        }
        $blacklistedToken = new BlackListedTocken($token);
        $this->entityManager->persist($blacklistedToken);
        $this->entityManager->flush();
    }

    public function isTokenBlacklisted(string $token): bool
    {
        $blacklistedToken = $this->entityManager->getRepository(BlackListedTocken::class)->findOneBy(['token' => $token]);

        return $blacklistedToken !== null;
    }
}
