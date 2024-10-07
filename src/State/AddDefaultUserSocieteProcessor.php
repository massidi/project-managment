<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Societe;
use App\Entity\SocieteUser;
use http\Exception\RuntimeException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

readonly class AddDefaultUserSocieteProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private ProcessorInterface $persistProcessor,
        private Security           $security,
    )
    {
    }

    /**
     * @return Societe|void
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {

        if (!$data instanceof Societe) {
            throw new RuntimeException();
        }
        $user = $this->security->getUser();

        $societeUser = new SocieteUser();
        $societeUser->setUser($user);
        $societeUser->setSociete($data);
        $societeUser->setAdmin(true);

        $data->addSocieteUser($societeUser);

        return $this->persistProcessor->process($data, $operation, $uriVariables, $context);
    }
}
