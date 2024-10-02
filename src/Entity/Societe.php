<?php

namespace App\Entity;

use App\Repository\SocieteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\GetCollection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    operations: [
        new Get(security: "is_granted('view', object)"), // Vue basée sur le rôle de l'utilisateur dans la société
        new Put(security: "is_granted('edit', object)"), // Modification réservée aux administrateurs et managers
        new Delete(security: "is_granted('delete', object)"), // Suppression réservée aux administrateurs
        new GetCollection, // Récupération de la collection des sociétés auxquelles l'utilisateur appartient
        new Post(securityPostDenormalize: "is_granted('create', object)") // Création réservée aux managers et administrateurs
    ], // Accès réservé aux utilisateurs authentifiés
    normalizationContext: ['groups' => ['societe:read']],
    denormalizationContext: ['groups' => ['societe:write']],
    security: "is_granted('ROLE_USER')"
)]
#[ORM\Entity(repositoryClass: SocieteRepository::class)]
class Societe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['societe:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['societe:read', 'societe:write'])]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Groups(['societe:read', 'societe:write'])]
    private ?string $numeroSiret = null;

    #[ORM\Column(length: 255)]
    #[Groups(['societe:read', 'societe:write'])]
    private ?string $adresse = null;

    /**
     * @var Collection<int, Projet>
     */
    #[ORM\OneToMany(targetEntity: Projet::class, mappedBy: 'societe')]
    #[Groups(['societe:read'])]
    private Collection $projets;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'societes')]
    #[Groups(['societe:read'])]
    private Collection $users;

    public function __construct()
    {
        $this->projets = new ArrayCollection();
        $this->users = new ArrayCollection();
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

    public function getNumeroSiret(): ?string
    {
        return $this->numeroSiret;
    }

    public function setNumeroSiret(string $numeroSiret): static
    {
        $this->numeroSiret = $numeroSiret;

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

    /**
     * @return Collection<int, Projet>
     */
    public function getProjets(): Collection
    {
        return $this->projets;
    }

    public function addProjet(Projet $projet): static
    {
        if (!$this->projets->contains($projet)) {
            $this->projets->add($projet);
            $projet->setSociete($this);
        }

        return $this;
    }

    public function removeProjet(Projet $projet): static
    {
        if ($this->projets->removeElement($projet)) {
            // set the owning side to null (unless already changed)
            if ($projet->getSociete() === $this) {
                $projet->setSociete(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        $this->users->removeElement($user);

        return $this;
    }
}
