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
    public function testProjectValidator1()
    {
        $project = new Projet();
        $project->setTitre("title");
        $project->setDescription("description");
        $project->setDateCreation(new \DateTime());
        $errors = $this->validator->validate($project);

        $this->assertEquals(
            "translates these two sentences: Your title must be at least 10 characters long",
            $errors[0]->getMessage()
        );
        $this->assertEquals(
            "Your description must be at least 20 characters long",
            $errors[1]->getMessage()
        );
    }

    public function testProjectValidator2()
    {
        $projet = new Projet();
        $projet->setTitre("Web Development with Symfony");
        $projet->setDescription("Course description on web development with Symfony. Author: Reddy semi");
        $projet->setDateCreation(new \DateTime());
        $errors = $this->validator->validate($projet);

        $this->assertEquals(
            0, count($errors)
        );

    }

}
