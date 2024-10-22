<?php

namespace App\Entity;

use ApiPlatform\Metadata\Patch;
use App\Repository\ProjetRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\GetCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => ['projet:read']],
            security: "is_granted('CAN_ACCESS_PROJECT', object)"
        ),
        new Patch(
            denormalizationContext: ['groups' => ['projet:update']],
            security: "is_granted('CAN_UPDATE_PROJECT_IN_SOCIETE', object)"
        ), // Managers and admins can modify
        new Delete(
            security: "is_granted('CAN_DELETE_PROJECT_IN_SOCIETE', object)"
        ), // Deletion reserved for administrators
        new GetCollection(
            normalizationContext: ['groups' => ['projet:read']],
            securityPostDenormalize: "is_granted('CAN_ACCESS_PROJECT', object)"), // List of projects accessible to all
        new Post(
            denormalizationContext: ['groups' => ['projet:write']],
            securityPostDenormalize: 'is_granted("CAN_CREATE_PROJECT_IN_SOCIETE", object)',
        ) // Creation reserved for managers and admins
    ], // Access to the API is reserved for authenticated users
    normalizationContext: ['groups' => ['projet:read', 'projet:update']],
    denormalizationContext: ['groups' => ['projet:write']],
    security: "is_granted('ROLE_USER')"
)]
#[ORM\Entity(repositoryClass: ProjetRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Projet
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['projet:read', 'societe:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 10,
        max: 50,
        minMessage: 'Your description must be at least {{ limit }} characters long',
        maxMessage: 'Your description cannot exceed {{ limit }} characters',
    )]
    #[Groups(['projet:read', 'projet:write', 'societe:read', 'projet:update'])]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    #[Assert\Length(
        min: 20,
        max: 100,
        minMessage: 'Your description must be at least {{ limit }} characters long',
        maxMessage: 'Your description cannot exceed {{ limit }} characters',
    )]
    #[Groups(['projet:read', 'projet:write', 'projet:update','societe:read'])]
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
    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->dateCreation = new \DateTimeImmutable();
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
