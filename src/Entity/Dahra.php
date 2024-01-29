<?php

namespace App\Entity;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\DahraRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: DahraRepository::class)]
class Dahra
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $adresse = null;

    #[ORM\Column(length: 255)]
    private ?string $region = null;

    #[ORM\Column(length: 255)]
    private ?string $nomOuztas = null;

    #[ORM\Column(length: 255)]
    private ?string $numeroTelephoneOuztas = null;

    #[ORM\OneToMany(mappedBy: 'Dahra', targetEntity: Talibe::class, orphanRemoval: true)]
    private Collection $talibes;

    #[ORM\OneToMany(mappedBy: 'Dahra', targetEntity: FaireDon::class, orphanRemoval: true)]
    private Collection $faireDons;

    #[ORM\Column]
    private ?int $nombreTalibe = null;
    #[ORM\ManyToOne(inversedBy: 'Users')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $User = null;
    
    
    public function __construct()
    {
        $this->talibes = new ArrayCollection();
        $this->faireDons = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(string $region): static
    {
        $this->region = $region;

        return $this;
    }

    public function getNomOuztas(): ?string
    {
        return $this->nomOuztas;
    }

    public function setNomOuztas(string $nomOuztas): static
    {
        $this->nomOuztas = $nomOuztas;

        return $this;
    }

    public function getNumeroTelephoneOuztas(): ?string
    {
        return $this->numeroTelephoneOuztas;
    }

    public function setNumeroTelephoneOuztas(string $numeroTelephoneOuztas): static
    {
        $this->numeroTelephoneOuztas = $numeroTelephoneOuztas;

        return $this;
    }

    /**
     * @return Collection<int, Talibe>
     */
    public function getTalibes(): Collection
    {
        return $this->talibes;
    }

    public function addTalibe(Talibe $talibe): static
    {
        if (!$this->talibes->contains($talibe)) {
            $this->talibes->add($talibe);
            $talibe->setDahra($this);
        }

        return $this;
    }

    public function removeTalibe(Talibe $talibe): static
    {
        if ($this->talibes->removeElement($talibe)) {
            // set the owning side to null (unless already changed)
            if ($talibe->getDahra() === $this) {
                $talibe->setDahra(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, FaireDon>
     */
    public function getFaireDons(): Collection
    {
        return $this->faireDons;
    }

    public function addFaireDon(FaireDon $faireDon): static
    {
        if (!$this->faireDons->contains($faireDon)) {
            $this->faireDons->add($faireDon);
            $faireDon->setDahra($this);
        }

        return $this;
    }

    public function removeFaireDon(FaireDon $faireDon): static
    {
        if ($this->faireDons->removeElement($faireDon)) {
            // set the owning side to null (unless already changed)
            if ($faireDon->getDahra() === $this) {
                $faireDon->setDahra(null);
            }
        }

        return $this;
    }

    public function getNombreTalibe(): ?int
    {
        return $this->nombreTalibe;
    }

    public function setNombreTalibe(int $nombreTalibe): static
    {
        $this->nombreTalibe = $nombreTalibe;

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
}
