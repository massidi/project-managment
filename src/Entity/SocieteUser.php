<?php

namespace App\Entity;

use App\Repository\SocieteUserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: SocieteUserRepository::class)]
#[UniqueEntity(
    fields: ['user', 'societe'],
    message: "It is impossible to assign a user to the same company twice.",
    errorPath: 'user',
)]
class SocieteUser
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class,  inversedBy: 'societes')]
    #[Groups([ 'societe:write'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Societe::class, inversedBy: 'societeUsers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Societe $societe = null;

    #[ORM\Column]
    #[Groups([ 'societe:write'])]
    /**
     * @Assert\Type(
     *     type="bool",
     *     message="La valeur doit être de type booléen."
     * )
     */
    private ?bool $isAdmin = false;

    #[ORM\Column]
    #[Groups([ 'societe:write'])]
    /**
     * @Assert\Type(
     *     type="bool",
     *     message="The value must be of boolean type."
     * )
     */
    private ?bool $isManager = false;

    #[ORM\Column]
    #[Groups(['societe:write'])]
    /**
     * @Assert\Type(
     *     type="bool",
     *     message="The value must be of boolean type."
     * )
     */
    private ?bool $isConsultant = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

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

    public function isAdmin(): ?bool
    {
        return $this->isAdmin;
    }

    public function setAdmin(bool $isAdmin): static
    {
        $this->isAdmin = $isAdmin;

        return $this;
    }

    public function isManager(): ?bool
    {
        return $this->isManager;
    }

    public function setManager(bool $isManager): static
    {
        $this->isManager = $isManager;

        return $this;
    }

    public function isConsultant(): ?bool
    {
        return $this->isConsultant;
    }

    public function setConsultant(bool $isConsultant): static
    {
        $this->isConsultant = $isConsultant;

        return $this;
    }

    #[Assert\Callback()]
    public function validate(ExecutionContextInterface $context, mixed $payload): void
    {
        $trueCount = 0;
        $trueCount += $this->isAdmin ? 1 : 0;
        $trueCount += $this->isManager ? 1 : 0;
        $trueCount += $this->isConsultant ? 1 : 0;
        if ($trueCount > 1) {
            $context
                ->buildViolation('Une personne ne peut pas avoir plusieurs les droits')
                ->atPath('user')
                ->addViolation()
            ;
        }
    }
}
