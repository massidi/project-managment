<?php

namespace App\Entity;

use ApiPlatform\Metadata\Patch;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\Parameter;
use App\ApiResource\AddUserSocieteInput;
use App\Repository\SocieteRepository;
use App\State\AddDefaultUserSocieteProcessor;
use App\State\AddUserSocieteProcessor;
use App\State\GetOneProjetBySocieteProvider;
use App\State\GetProjetSocieteProvider;
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
use Symfony\Component\Validator\Constraints as Assert;


#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => ['societe:detail']],
            security: "is_granted('CAN_ACCESS_SOCIETE', object)",
        ), // Vue basée sur le rôle de l'utilisateur dans la société
        new Put(
            security: "is_granted('CAN_UPDATE_SOCIETE', object)"
        ), // Modification réservée aux administrateurs et managers
        new Patch(
            security: "is_granted('CAN_UPDATE_SOCIETE', object)"
        ), // Modification réservée aux administrateurs et managers
        new Delete(
            security: "is_granted('CAN_DELETE_SOCIETE', object)"
        ), // Suppression réservée aux administrateurs
        new GetCollection(
        ), // Récupération de la collection des sociétés auxquelles l'utilisateur appartient
        new Post(
            securityPostDenormalize: "is_granted('CAN_CREATE_SOCIETE', object)",
            processor: AddDefaultUserSocieteProcessor::class
        ), // Création réservée aux managers et administrateurs

        new Post(
            uriTemplate: '/societe/{id}/user',
            openapi: new Operation(
                operationId: "Assigner un utilisateur dans une société avec ses droits",
                responses  : [
                    '200' => [
                        'description' => "Assigner un utilisateur dans une société avec ses droits",
                    ],
                ],
                summary    :  "Assigner un utilisateur dans une société avec ses droits",
                description:  "Assigner un utilisateur dans une société avec ses droits",
            ),
            description: 'Assigner un utilisateur dans une société avec ses droits',
            input: AddUserSocieteInput::class,
            processor: AddUserSocieteProcessor::class
        ), // Affecte un utilisateur dans une société
        new Get(
            uriTemplate: '/societe/{id}/projets',
            openapi: new Operation(
                operationId: "Récuperation de la liste des projets d'une société",
                responses  : [
                    '200' => [
                        'description' => "Récuperation de la liste des projets d'une société",
                    ],
                ],
                summary    :  "Récuperation de la liste des projets d'une société",
                description:  "Récuperation de la liste des projets d'une société",
            ),
            description: "Récuperation de la liste des projets d'une société",
            securityPostDenormalize: "is_granted('CAN_ACCESS_SOCIETE', object)",
            provider: GetProjetSocieteProvider::class,
        ), //Récupérer la liste des projets d'une société à laquelle ils appartiennent.
        new Get(
            uriTemplate: '{id_societe}/societe/{id_projet}/projets',
            openapi: new Operation(
                operationId: "Consulter les détails d’un projet spécifique au sein d'une société.",
                responses  : [
                    '200' => [
                        'description' => "Consulter les détails d’un projet spécifique au sein d'une société.",
                    ],
                ],
                summary    :  "Consulter les détails d’un projet spécifique au sein d'une société.",
                description:  "Consulter les détails d’un projet spécifique au sein d'une société.",
                parameters : [
                    new Parameter(
                        name: 'id_societe',
                        in: 'path',
                        description: 'id société',
                        required: true,
                        schema: ['type' => 'integer', 'min' => 1],
                    ),
                    new Parameter(
                        name: 'id_projet',
                        in: 'path',
                        description: 'id projet',
                        required: true,
                        schema: ['type' => 'integer', 'min' => 1],
                    )
                ]
            ),
            description: "Récuperation d'un projet spécifique d'une société",
            provider: GetOneProjetBySocieteProvider::class,
        ), //Consulter les détails d’un projet spécifique au sein d'une société.
    ],
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

    #[Assert\NotBlank]
    #[Assert\Length(
        min: 10,
        max: 40,
        minMessage: 'Your first name must be at least {{ limit }} characters long',
        maxMessage: 'Your first name cannot be longer than {{ limit }} characters',
    )]
    #[ORM\Column(length: 255)]
    #[Groups(['societe:read', 'societe:write'])]
    private ?string $nom = null;

    #[Assert\NotBlank]
    #[Assert\Regex(
        pattern: '/\d{14}/',
        message: 'Votre numéro siret incorrect'
    )]
    #[ORM\Column(length: 255)]
    #[Groups(['societe:read', 'societe:write'])]
    private ?string $numeroSiret = null;

    #[Assert\NotBlank]
    #[Assert\Length(
        min: 10,
        max: 50,
        minMessage: 'Votre adresse doit être au minimum {{ limit }} caractères longs',
        maxMessage: 'Votre adresse ne peut pas dépasse {{ limit }} caractères',
    )]
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
    #[ORM\OneToMany(targetEntity: SocieteUser::class, mappedBy: 'societe',cascade: ["persist"])]
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
