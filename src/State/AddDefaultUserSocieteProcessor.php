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
     * Processes the given Societe entity and adds the current user as a default user.
     *
     * @param mixed $data The data to process, must be an instance of Societe.
     * @param Operation $operation The operation context.
     * @param array $uriVariables The URI variables.
     * @param array $context Additional context options.
     * @return Societe|void The processed Societe entity or void.
     * @throws RuntimeException if the data is not an instance of Societe.
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
