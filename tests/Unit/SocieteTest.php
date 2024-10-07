<?php

namespace App\Tests\Unit;

use App\Entity\Projet;
use App\Entity\Societe;
use App\Entity\SocieteUser;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class SocieteTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function testProjet(Societe $societe, User $user)
    {
        self::assertTrue($societe->isConsultant($user));
        self::assertTrue($societe->isMember($user));
        self::assertFalse($societe->isManager($user));
        self::assertFalse($societe->isAdmin($user));
    }

    public function dataProvider(): array
    {
        $user = new User();
        $societes = new Societe();
        $societeUser= new SocieteUser();
        $societeUser->setUser($user);
        $societeUser->setSociete($societes);
        $societeUser->setConsultant(true);
        $societeUser->setManager(false);
        $societeUser->setAdmin(false);
        $societes->addSocieteUser($societeUser);
        return [
            [
                $societes,$user
            ]
        ];
    }
}
