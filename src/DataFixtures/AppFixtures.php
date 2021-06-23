<?php

namespace App\DataFixtures;

use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /** @var UserPasswordEncoderInterface $encoder */
    private $encoder;

    /** @var Generator */
    protected $faker;

    public function __construct(UserPasswordEncoderInterface $encoder, Generator $faker)
    {
        $this->encoder = $encoder;
        $this->faker = $faker;
    }

    public function load(ObjectManager $manager)
    {
        $manager->flush();
    }
}
