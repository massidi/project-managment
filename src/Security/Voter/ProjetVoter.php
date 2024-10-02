<?php

namespace App\Security\Voter;

use App\Entity\Projet;
use App\Entity\User;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ProjetVoter extends Voter
{
    const EDIT = 'edit';
    const DELETE = 'delete';
    const VIEW = 'view';

    // Role constants
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_MANAGER = 'ROLE_MANAGER';
    const ROLE_CONSULTANT = 'ROLE_CONSULTANT';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::DELETE, self::VIEW])
            && $subject instanceof Projet;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // If the user is not logged in, deny access
        if (!$user instanceof User) {
            return false;
        }

        /** @var Projet $projet */
        $projet = $subject;

        // Get the user's roles (global roles for now)
        $userRoles = $user->getRoles();

        // Build expected role strings for the user's role in this specific company
        $companyRoleAdmin = self::ROLE_ADMIN . '_' . $projet->getSociete()->getId();
        $companyRoleManager = self::ROLE_MANAGER . '_' . $projet->getSociete()->getId();
        $companyRoleConsultant = self::ROLE_CONSULTANT . '_' . $projet->getSociete()->getId();

        switch ($attribute) {
            case self::EDIT:
            case self::DELETE:
                // Only admin or manager can edit/delete projects
                return in_array($companyRoleAdmin, $userRoles) || in_array($companyRoleManager, $userRoles);

            case self::VIEW:
                // Any role (admin, manager, consultant) can view the project
                return in_array($companyRoleAdmin, $userRoles) ||
                    in_array($companyRoleManager, $userRoles) ||
                    in_array($companyRoleConsultant, $userRoles);
        }

        return false;
    }
}
