<?php

namespace App\Security\Voter;

use App\Entity\Projet;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class CanAccessProjetVoter extends Voter
{
    public const string PERMISSION = 'CAN_ACCESS_PROJECT';

    /**
     * Determines if the attribute and subject are supported.
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        return self::PERMISSION === $attribute && $subject instanceof Projet;
    }

    /**
     * Checks if the user has permission to access the project.
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {

        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof User) {
            return false;
        }

        if (!$subject instanceof Projet) {
            return false;
        }

        return $subject->isMember($user);

    }
}
