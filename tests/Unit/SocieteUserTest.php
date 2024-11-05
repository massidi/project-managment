<?php

namespace App\Tests\Unit;

use App\Entity\Societe;
use App\Entity\SocieteUser;
use App\Entity\User;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SocieteUserTest extends KernelTestCase
{
    private $validator;
    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();
        $this->validator = $this->getContainer()->get('validator');
    }

    /**
     * @dataProvider dataProvider
     */
    public function testProjet(SocieteUser $societeUser, $message)
    {
        $errors = $this->validator->validate($societeUser);
        $this->assertCount(1, $errors);
        $this->assertEquals(
            $message,
            $errors[0]->getMessage()
        );
    }

    public function dataProvider(): array
    {

        $societeUser= new SocieteUser();
        $societeUser->setConsultant(true);
        $societeUser->setManager(true);
        $societeUser->setAdmin(false);
        return [
            [
                $societeUser,'A person cannot have multiple rights.'
            ]
        ];
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->validator = null;
    }
}
