<?php

namespace App\Extention;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Societe;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;


readonly class MemberOfSocieteExtention implements QueryCollectionExtensionInterface
{


    public function __construct(private Security $security)
    {

    }

    /**
     * Applies a filter to the provided QueryBuilder to restrict results
     * based on the current user's associated Societe entities.
     *
     * @param QueryBuilder $queryBuilder The QueryBuilder instance to modify.
     * @param QueryNameGeneratorInterface $queryNameGenerator The query name generator.
     * @param string $resourceClass The resource class to ensure proper filtering.
     * @param Operation|null $operation The operation being performed, if any.
     * @param array $context Additional context for the operation.
     */
    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?Operation $operation = null, array $context = []): void
    {
        $user = $this->security->getUser();
        if (!$user instanceof User || !is_a($resourceClass, Societe::class, true)) {
            return;
        }
        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->join($rootAlias . '.societeUsers', 'societeUsers')
            ->andWhere('societeUsers.user = :user')
            ->setParameter('user', $user);
    }
}
