<?php

namespace App\Tests\Unit;

use App\Entity\Projet;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProjetTest extends KernelTestCase
{
    private $validator;
    protected function setUp(): void
    {
        self::bootKernel();
        $this->validator = $this->getContainer()->get('validator');
    }
    public function testProjetValidator1()
    {
        $projet = new Projet();
        $projet->setTitre("titre");
        $projet->setDescription("description");
        $projet->setDateCreation(new \DateTime());
        $errors = $this->validator->validate($projet);

        $this->assertEquals(
            "Votre titre doit être au minimum 10 caractères longs",
            $errors[0]->getMessage()
        );
        $this->assertEquals(
            "Votre description doit être au minimum 20 caractères longs",
            $errors[1]->getMessage()
        );
    }

    public function testProjetValidator2()
    {
        $projet = new Projet();
        $projet->setTitre("développement web avec symfony");
        $projet->setDescription("Description du cours développement web avec symfony. Auteur: Reddy semi");
        $projet->setDateCreation(new \DateTime());
        $errors = $this->validator->validate($projet);

        $this->assertEquals(
            0, count($errors)
        );

    }

}
