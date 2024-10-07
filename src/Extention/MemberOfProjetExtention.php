<?php

namespace App\Extention;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Projet;
use App\Entity\Societe;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;


readonly class MemberOfProjetExtention implements QueryCollectionExtensionInterface
{

    public function __construct(private Security $security)
    {

    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?Operation $operation = null, array $context = []): void
    {
        $user = $this->security->getUser();
        if (  !$user instanceof User || !is_a($resourceClass, Projet::class, true)) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $queryBuilder->join($rootAlias.'.societe', 'societe')
                ->addselect('societe')
                ->join('societe.societeUsers', 'societeUsers')
                ->andWhere('societeUsers.user = :user')
                ->setParameter('user', $user);
    }
}
