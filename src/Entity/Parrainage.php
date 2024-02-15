<?php

namespace App\Entity;

use App\Repository\ParrainageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParrainageRepository::class)]
class Parrainage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 255)]
    private ?string $typeParrainage = 'ndeyeDahra'; // Valeur par défaut

    #[ORM\Column(length: 255)]
    private ?string $status = 'en cours'; // Valeur par défaut


    #[ORM\ManyToOne(inversedBy: 'parrainages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Talibe $talibe = null;

    #[ORM\ManyToOne(inversedBy: 'parrainages')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getTypeParrainage(): ?string
    {
        return $this->typeParrainage;
    }

    public function setTypeParrainage(string $typeParrainage): static
    {
        if (!in_array($typeParrainage, ['ndeyeDahra', 'parrainageMensuel'])) {
            throw new \InvalidArgumentException("Type de parrainage invalide");
        }
        $this->typeParrainage = $typeParrainage;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        if (!in_array($status, ['valide', 'rejeter', 'en cours'])) {
            throw new \InvalidArgumentException("Status invalide");
        }
        $this->status = $status;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }
    
    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }
    
    public function getTalibe(): ?Talibe
    {
        return $this->talibe;
    }
    
    public function setTalibe(?Talibe $talibe): self
    {
        $this->talibe = $talibe;
        return $this;
    }
    
}
