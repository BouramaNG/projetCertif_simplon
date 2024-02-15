<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups(["user"])]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255, nullable: true)]
   #[Groups(["user"])]
    private ?string $nom = null;

    #[ORM\Column(length: 255, nullable: true)]
   #[Groups(["user"])]
    private ?string $prenom = null;

    #[ORM\Column(length: 255, nullable: true)]
   #[Groups(["user"])]
    private ?string $adresse = null;

    #[ORM\Column(length: 255)]
   #[Groups(["user"])]
    private ?string $numeroTelephone = null;

    #[ORM\OneToMany(mappedBy: 'User', targetEntity: Parrainage::class, orphanRemoval: true)]
    private Collection $parrainages;

    #[ORM\OneToMany(mappedBy: 'User', targetEntity: FaireDon::class, orphanRemoval: true)]
    private Collection $faireDons;

    #[ORM\OneToMany(mappedBy: 'User', targetEntity: Dahra::class, orphanRemoval: true)]
    private Collection $dahras;

    #[ORM\OneToMany(mappedBy: 'User', targetEntity: Newsletter::class)]
    private Collection $newsletters;

    #[ORM\Column(type: 'boolean')]
   private $isActive = false;

    #[ORM\Column(length: 2000, nullable: true)]
    private ?string $resetToken = null;

    public function __construct()
    {
        $this->parrainages = new ArrayCollection();
        $this->faireDons = new ArrayCollection();
        $this->dahras = new ArrayCollection();
        $this->newsletters = new ArrayCollection();
        $this->isActive = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getNumeroTelephone(): ?string
    {
        return $this->numeroTelephone;
    }

    public function setNumeroTelephone(string $numeroTelephone): static
    {
        $this->numeroTelephone = $numeroTelephone;

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
            $parrainage->setUser($this);
        }

        return $this;
    }

    public function removeParrainage(Parrainage $parrainage): static
    {
        if ($this->parrainages->removeElement($parrainage)) {
            // set the owning side to null (unless already changed)
            if ($parrainage->getUser() === $this) {
                $parrainage->setUser(null);
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
            $faireDon->setUser($this);
        }

        return $this;
    }

    public function removeFaireDon(FaireDon $faireDon): static
    {
        if ($this->faireDons->removeElement($faireDon)) {
            // set the owning side to null (unless already changed)
            if ($faireDon->getUser() === $this) {
                $faireDon->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Dahra>
     */
    public function getDahras(): Collection
    {
        return $this->dahras;
    }

    public function addDahra(Dahra $dahra): static
    {
        if (!$this->dahras->contains($dahra)) {
            $this->dahras->add($dahra);
            $dahra->setUser($this);
        }

        return $this;
    }

    public function removeDahra(Dahra $dahra): static
    {
        if ($this->dahras->removeElement($dahra)) {
            // set the owning side to null (unless already changed)
            if ($dahra->getUser() === $this) {
                $dahra->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Newsletter>
     */
    public function getNewsletters(): Collection
    {
        return $this->newsletters;
    }

    public function addNewsletter(Newsletter $newsletter): static
    {
        if (!$this->newsletters->contains($newsletter)) {
            $this->newsletters->add($newsletter);
            $newsletter->setUser($this);
        }

        return $this;
    }

    public function removeNewsletter(Newsletter $newsletter): static
    {
        if ($this->newsletters->removeElement($newsletter)) {
            // set the owning side to null (unless already changed)
            if ($newsletter->getUser() === $this) {
                $newsletter->setUser(null);
            }
        }

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetToken(?string $resetToken): static
    {
        $this->resetToken = $resetToken;

        return $this;
    }
}
