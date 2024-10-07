<?php

namespace App\Security\Voter;

use App\Entity\Societe;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class CanDeleteSocieteVoter extends Voter
{
    public const string PERMISSION = 'CAN_DELETE_SOCIETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return self::PERMISSION === $attribute && $subject instanceof Societe;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {

        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof User) {
            return false;
        }
        // if the user is anonymous, do not grant access
        if (!$subject instanceof Societe) {
            return false;
        }

        return $subject->isAdmin($user);
    }
}
