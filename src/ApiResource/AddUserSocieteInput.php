<?php

namespace App\ApiResource;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
class AddUserSocieteInput
{
    #[Groups(['societe:write'])]
    #[Assert\NotBlank]
    #[Assert\Positive]
    public int $userId;

    #[Groups(['societe:write'])]
    public bool $isAdmin = false;

    #[Groups(['societe:write'])]
    public bool $isManager = false;

    #[Groups(['societe:write'])]
    public bool $isConsultant = false;

}
