<?php
namespace App\Serializer;

use App\Entity\Dahra;
use Symfony\Component\Serializer\Annotation\SerializedName;

class DahraSerializer
{
    #[SerializedName("id")]
    private ?int $id;

    #[SerializedName("nom")]
    private ?string $nom;

    #[SerializedName("adresse")]
    private ?string $adresse;

    #[SerializedName("region")]
    private ?string $region;

    #[SerializedName("nomOuztas")]
    private ?string $nomOuztas;

    #[SerializedName("numeroTelephoneOuztas")]
    private ?string $numeroTelephoneOuztas;

    #[SerializedName("nombreTalibe")]
    private ?int $nombreTalibe;

    #[SerializedName("imageFilename")]
    private ?string $imageFilename;

    public function __construct(Dahra $dahra)
    {
        $this->id = $dahra->getId();
        $this->nom = $dahra->getNom();
        $this->adresse = $dahra->getAdresse();
        $this->region = $dahra->getRegion();
        $this->nomOuztas = $dahra->getNomOuztas();
        $this->numeroTelephoneOuztas = $dahra->getNumeroTelephoneOuztas();
        $this->nombreTalibe = $dahra->getNombreTalibe();
        $this->imageFilename = $dahra->getImageFilename();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function getNomOuztas(): ?string
    {
        return $this->nomOuztas;
    }

    public function getNumeroTelephoneOuztas(): ?string
    {
        return $this->numeroTelephoneOuztas;
    }

    public function getNombreTalibe(): ?int
    {
        return $this->nombreTalibe;
    }

    public function getImageFilename(): ?string
    {
        return $this->imageFilename;
    }
}
