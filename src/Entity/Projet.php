<?php

namespace App\Entity;

use ApiPlatform\Metadata\Patch;
use App\Repository\ProjetRepository;
use Doctrine\DBAL\Types\Types;
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
        new Get(normalizationContext: ['groups' => ['projet:read']], security: "is_granted('view', object)"), // Les consultants peuvent consulter
        new Put(denormalizationContext: ['groups' => ['projet:write']], security: "is_granted('edit', object)"), // Les managers et admins peuvent modifier
        new Delete(security: "is_granted('delete', object)"), // Suppression réservée aux administrateurs
        new GetCollection(normalizationContext: ['groups' => ['projet:read']]), // Liste des projets accessibles à tous
        new Post(denormalizationContext: ['groups' => ['projet:write']], securityPostDenormalize: "is_granted('edit', object)") // Création réservée aux managers et admins
    ], // L'accès à l'API est réservé aux utilisateurs authentifiés
    normalizationContext: ['groups' => ['projet:read']],
    denormalizationContext: ['groups' => ['projet:write']],
    security: "is_granted('ROLE_USER')"
)]
#[ORM\Entity(repositoryClass: ProjetRepository::class)]
class Projet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['projet:read', 'societe:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['projet:read', 'projet:write', 'societe:read'])]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['projet:read', 'projet:write'])]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['projet:read'])]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\ManyToOne(inversedBy: 'projets')]
    #[Groups(['projet:read', 'projet:write'])]
    private ?Societe $societe = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

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

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): static
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getSociete(): ?Societe
    {
        return $this->societe;
    }

    public function setSociete(?Societe $societe): static
    {
        $this->societe = $societe;

        return $this;
    }
    /**
     * @param User $user
     * @return bool
     */
    public function isAdmin(User $user): bool {
        return $this->getSociete()->isAdmin($user);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function isManager(User $user): bool {
        return $this->getSociete()->isManager($user);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function isMember(User $user): bool {
        return $this->getSociete()->isMember($user);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function isConsultant(User $user): bool {
        return $this->getSociete()->isConsultant($user);
    }

}
