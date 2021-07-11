<?php

namespace App\DataFixtures;

use DateTime;
use App\Entity\Raid;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Server;
use App\Entity\Faction;
use App\Entity\Timezone;
use App\Entity\Character;
use App\Entity\RaidCharacter;
use App\Entity\CharacterClass;
use App\Service\Raid\Identifier;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /** @var UserPasswordEncoderInterface */
    private $encoder;

    /** @var Identifier */
    private $identifier;

    public function __construct(UserPasswordEncoderInterface $encoder, Identifier $identifier)
    {
        $this->encoder = $encoder;
        $this->identifier = $identifier;
    }

    public function load(ObjectManager $manager)
    {
        // Users

        $timezones = $manager->getRepository(Timezone::class)->findBy([], [], 50);
        for ($i = 0; $i <= 40; $i++) {
            $user = new User();
            $user
                ->setName('User_' . $i)
                ->setEmail('user' . $i . '@user.fr')
                ->setRoles(['ROLE_RAID_LEADER'])
                ->setPassword($this->encoder->encodePassword($user, 'password'))
                ->setTimezone($timezones[rand(0, 49)])
                ->setStatus(1)
                ->setNbrOfAttempt(1)
                ->setLastAttempt(new DateTime());

            $manager->persist($user);
        }


        // Characters

        // $users = $manager->getRepository(User::class)->findAll();
        // $faction = $manager->getRepository(Faction::class)->find(1);
        // $server = $manager->getRepository(Server::class)->find(1);
        // $classes = $manager->getRepository(CharacterClass::class)->findAll();
        // $roles = $manager->getRepository(Role::class)->findAll();

        // foreach ($users as $key => $user) {
        //     $character = new Character();
        //     $character
        //         ->setUser($user)
        //         ->setName('character_' . $key)
        //         ->setFaction($faction)
        //         ->setServer($server)
        //         ->setCharacterClass($classes[rand(0, 8)])
        //         ->addRole($roles[rand(0, 2)])
        //         ->addRole($roles[rand(0, 2)])
        //         ->addRole($roles[rand(0, 2)])
        //         ->setInformation('Informations')
        //         ->setIsArchived(false);

        //     $manager->persist($character);
        //     $characters[] = $character;
        // }

        $manager->flush();
    }
}
