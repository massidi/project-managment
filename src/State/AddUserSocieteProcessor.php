<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\AddUserSocieteInput;
use App\Entity\SocieteUser;
use App\Repository\SocieteRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

readonly class AddUserSocieteProcessor implements ProcessorInterface
{


    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly SocieteRepository      $societeRepository,
        private readonly UserRepository         $userRepository,
        private readonly AuthorizationCheckerInterface $authorizationChecker,
        private readonly ValidatorInterface $validator
    )
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): void
    {
        $idSociete = $uriVariables['id'] ?? null;
        $societe = $this->societeRepository->find($idSociete);

        if (!$societe) {
            throw new NotFoundHttpException("La societe n'existe pas.");
        }

        if (!$this->authorizationChecker->isGranted('CAN_ADD_USER_IN_SOCIETE', $societe)) {
            throw new AccessDeniedHttpException("Vous n'avez pas les droits nécessaires pour effectuer cette action.");
        }

        $user = $this->userRepository->find($data->userId);
        if (!$user) {
            throw new NotFoundHttpException("L'utilisateur n'existe pas.");
        }

        if (!$data instanceof AddUserSocieteInput){
            return;
        }

        $societeUser = new SocieteUser();
        $societeUser->setUser($user);
        $societeUser->setSociete($societe);
        $societeUser->setAdmin($data->isAdmin);
        $societeUser->setConsultant($data->isConsultant);
        $societeUser->setManager($data->isManager);

        $violations = $this->validator->validate($societeUser);

        if (count($violations) > 0) {
            $errors = (string) $violations;
            throw new BadRequestHttpException($errors);
        }


        $this->entityManager->persist($societeUser);
        $this->entityManager->flush();
    }
}
