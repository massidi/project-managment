<?php

namespace App\Security\Voter;

use App\Entity\Projet;
use App\Entity\Societe;
use App\Entity\User;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ProjetVoter extends Voter
{
    public const string PERMISSION = 'CAN_ADD_USER_IN_SOCIETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return self::PERMISSION === $attribute && $subject instanceof Societe;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // If the user is not logged in, deny access
        if (!$user instanceof User || !$subject instanceof Societe ) {
            return false;
        }

        return $subject->isAdmin($user);

    }
}
