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
     * @var Collection<int, SocieteUser>
     */
    #[ORM\OneToMany(targetEntity: SocieteUser::class, mappedBy: 'Societe')]
    private Collection $societeUsers;

    public function __construct()
    {
        $this->projets = new ArrayCollection();
        $this->societeUsers = new ArrayCollection();
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
     * @return Collection<int, SocieteUser>
     */
    public function getSocieteUsers(): Collection
    {
        return $this->societeUsers;
    }

    public function addSocieteUser(SocieteUser $societeUser): static
    {
        if (!$this->societeUsers->contains($societeUser)) {
            $this->societeUsers->add($societeUser);
            $societeUser->setSociete($this);
        }

        return $this;
    }

    public function removeSocieteUser(SocieteUser $societeUser): static
    {
        if ($this->societeUsers->removeElement($societeUser)) {
            // set the owning side to null (unless already changed)
            if ($societeUser->getSociete() === $this) {
                $societeUser->setSociete(null);
            }
        }

        return $this;
    }
    /**
     * @param User $userParams
     * @return bool
     */
    public function isAdmin(User $userParams): bool {
        foreach ($this->getSocieteUsers() as $userSociete) {
            if ($userParams->getId() === $userSociete->getUser()->getId()
                &&  $userSociete->isAdmin()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param User $userParams
     * @return bool
     */
    public function isManager(User $userParams): bool {
        foreach ($this->getSocieteUsers() as $userSociete) {
            if ($userParams->getId() === $userSociete->getUser()->getId()
                &&  $userSociete->isManager()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param User $userParams
     * @return bool
     */
    public function isMember(User $userParams): bool {
        foreach ($this->getSocieteUsers() as $userSociete) {
            if ($userParams->getId() === $userSociete->getUser()->getId()){
                return true;
            }
        }

        return false;
    }

    /**
     * @param User $userParams
     * @return bool
     */
    public function isConsultant(User $userParams): bool {
        foreach ($this->getSocieteUsers() as $userSociete) {
            if ($userParams->getId() === $userSociete->getUser()->getId()
                &&  $userSociete->isConsultant()) {
                return true;
            }
        }

        return false;
    }
}
