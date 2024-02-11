<?php

namespace App\Entity;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\DahraRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: DahraRepository::class)]
#[Vich\Uploadable]
class Dahra
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["dahra"])]
    private ?string $nom = null;
    #[ORM\Column(length: 255)]
    #[Groups(["dahra"])]
    private ?string $adresse = null;
  

    #[ORM\Column(length: 255)]
    #[Groups(["dahra"])]
    private ?string $region = null;

    #[ORM\Column(length: 255)]
    #[Groups(["dahra"])]
    private ?string $nomOuztas = null;

    #[ORM\Column(length: 255)]
    #[Groups(["dahra"])]
    private ?string $numeroTelephoneOuztas = null;

    #[ORM\OneToMany(mappedBy: 'Dahra', targetEntity: Talibe::class, orphanRemoval: true)]
    private Collection $talibes;

    #[ORM\OneToMany(mappedBy: 'Dahra', targetEntity: FaireDon::class, orphanRemoval: true)]
    private Collection $faireDons;

    #[ORM\Column]
    #[Groups(["dahra"])]
    private ?int $nombreTalibe = null;
    #[ORM\ManyToOne(inversedBy: 'Users')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $User = null;

    #[ORM\Column(length: 255, nullable: true)]
    
    #[Vich\UploadableField(mapping: 'dahra_images', fileNameProperty: 'imageFilename')]
    private ?File $imageFile = null;

    #[ORM\Column(nullable: true)]
    #[Groups(["dahra"])]
    private ?string $imageFilename = null;

   
    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    
    public function __construct()
    {
        $this->talibes = new ArrayCollection();
        $this->faireDons = new ArrayCollection();
        // $this->imageFilename = null;
        // $this->imageFile = null;
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

    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageFilename(?string $imageFilename): void
{
    $this->imageFilename = $imageFilename;
}

public function getImageFilename(): ?string
{
    return $this->imageFilename;
}



}
