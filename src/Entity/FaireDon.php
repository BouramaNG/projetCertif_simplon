<?php

namespace App\Entity;

use App\Repository\FaireDonRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FaireDonRepository::class)]
class FaireDon
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $typeDon = null;

    #[ORM\ManyToOne(inversedBy: 'faireDons')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Dahra $Dahra = null;

    #[ORM\ManyToOne(inversedBy: 'faireDons')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $User = null;

    #[ORM\Column(length: 255)]
    private ?string $adresseProvenance = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $descriptionDon = null;

    #[ORM\Column(length: 255)]
    private ?string $disponibiliteDon = null;

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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getTypeDon(): ?string
    {
        return $this->typeDon;
    }

    public function setTypeDon(?string $typeDon): static
    {
        $this->typeDon = $typeDon;

        return $this;
    }

    public function getDahra(): ?Dahra
    {
        return $this->Dahra;
    }

    public function setDahra(?Dahra $Dahra): static
    {
        $this->Dahra = $Dahra;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->User;
    }

    public function setUser(?User $User): static
    {
        $this->User = $User;

        return $this;
    }

    public function getAdresseProvenance(): ?string
    {
        return $this->adresseProvenance;
    }

    public function setAdresseProvenance(string $adresseProvenance): static
    {
        $this->adresseProvenance = $adresseProvenance;

        return $this;
    }

    public function getDescriptionDon(): ?string
    {
        return $this->descriptionDon;
    }

    public function setDescriptionDon(string $descriptionDon): static
    {
        $this->descriptionDon = $descriptionDon;

        return $this;
    }

    public function getDisponibiliteDon(): ?string
    {
        return $this->disponibiliteDon;
    }

    public function setDisponibiliteDon(string $disponibiliteDon): static
    {
        $this->disponibiliteDon = $disponibiliteDon;

        return $this;
    }
}
