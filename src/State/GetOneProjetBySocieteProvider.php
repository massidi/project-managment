<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Projet;
use App\Repository\SocieteRepository;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

readonly class GetOneProjetBySocieteProvider implements ProviderInterface
{

    public function __construct(
        private SocieteRepository             $societeRepository,
        private AuthorizationCheckerInterface $authorizationChecker
    )
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $idSociete = $uriVariables['id_societe'] ?? null;
        $idProjet = $uriVariables['id_projet'] ?? null;
        $societe = $this->societeRepository->find($idSociete);
        if (!$this->authorizationChecker->isGranted('CAN_ACCESS_SOCIETE', $societe)) {
            throw new AccessDeniedHttpException('Access Denied.');
        }

        $projet = $societe->getProjets()->filter(function (Projet $projet) use ($idProjet) {
            return $projet->getId() == $idProjet;
        })->first();

        if (!$projet instanceof Projet) {
            throw new NotFoundHttpException("Le projet n'existe pas dans cette société.");
        }

        return $projet;
    }
}
