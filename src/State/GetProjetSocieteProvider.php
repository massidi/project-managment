<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Societe;
use App\Repository\SocieteRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

readonly class GetProjetSocieteProvider implements ProviderInterface
{
    /**
     * @param SocieteRepository $societeRepository
     * @param Security $security
     */
    public function __construct(
        private SocieteRepository             $societeRepository,
        private AuthorizationCheckerInterface $authorizationChecker
    )
    {
    }

    /**
     * @param Operation $operation
     * @param array $uriVariables
     * @param array $context
     * @return object|array|object[]|null
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $id = $uriVariables['id'] ?? null;
        /** @var Societe $societe */
        $societe = $this->societeRepository->find($id);
        if (!$societe){
            throw new NotFoundHttpException("No company corresponds to the id " . $id);        }

        if (!$this->authorizationChecker->isGranted('CAN_ACCESS_SOCIETE', $societe)) {
            throw new AccessDeniedHttpException('Access Denied.');
        }

        return $societe->getProjets();
    }
}
