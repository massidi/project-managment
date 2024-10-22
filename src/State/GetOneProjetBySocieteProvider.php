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

    /**
     * Provides a project based on the provided operation and URI variables.
     *
     * @param Operation $operation The operation to be processed.
     * @param array $uriVariables Variables extracted from the URI, including 'id_societe' and 'id_projet'.
     * @param array $context Additional context for processing the request.
     * @return object|array|null The project object if found, otherwise null.
     * @throws AccessDeniedHttpException If access is denied to the specified company.
     * @throws NotFoundHttpException If the specified project does not exist in the company.
     */
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
            throw new NotFoundHttpException("The project does not exist in this company.");
        }

        return $projet;
    }
}
