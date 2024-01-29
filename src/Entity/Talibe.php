<?php

namespace App\Entity;

use App\Repository\TalibeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TalibeRepository::class)]
class Talibe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $prenom = null;

    #[ORM\Column]
    private ?int $age = null;

    #[ORM\Column(length: 255)]
    private ?string $adresse = null;

    #[ORM\Column(length: 255)]
    private ?string $situation = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\ManyToOne(inversedBy: 'talibes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Dahra $Dahra = null;

    #[ORM\OneToMany(mappedBy: 'Talibe', targetEntity: Parrainage::class, orphanRemoval: true)]
    private Collection $parrainages;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $DateArriveTalibe = null;

    #[ORM\Column(length: 255)]
    private ?string $presenceTalibe = 'present'; // Valeur par défaut

    public function __construct()
    {
        $this->parrainages = new ArrayCollection();
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): static
    {
        $this->age = $age;

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

    public function getSituation(): ?string
    {
        return $this->situation;
    }

    public function setSituation(string $situation): static
    {
        $this->situation = $situation;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

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

    /**
     * @return Collection<int, Parrainage>
     */
    public function getParrainages(): Collection
    {
        return $this->parrainages;
    }

    public function addParrainage(Parrainage $parrainage): static
    {
        if (!$this->parrainages->contains($parrainage)) {
            $this->parrainages->add($parrainage);
            $parrainage->setTalibe($this);
        }

        return $this;
    }

    public function removeParrainage(Parrainage $parrainage): static
    {
        if ($this->parrainages->removeElement($parrainage)) {
            // set the owning side to null (unless already changed)
            if ($parrainage->getTalibe() === $this) {
                $parrainage->setTalibe(null);
            }
        }

        return $this;
    }

    public function getDateArriveTalibe(): ?\DateTimeInterface
    {
        return $this->DateArriveTalibe;
    }

    public function setDateArriveTalibe(?\DateTimeInterface $DateArriveTalibe): static
    {
        $this->DateArriveTalibe = $DateArriveTalibe;

        return $this;
    }

    public function getPresenceTalibe(): ?string
    {
        return $this->presenceTalibe;
    }

  

    public function setPresenceTalibe(?string $presenceTalibe): static
    {
        if (!in_array($presenceTalibe, ['present', 'sortie'])) {
            throw new \InvalidArgumentException("Status invalide");
        }
        $this->presenceTalibe = $presenceTalibe;

        return $this;
    }
    
}
