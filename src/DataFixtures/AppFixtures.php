<?php

namespace App\DataFixtures;

use DateTime;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Server;
use App\Entity\Faction;
use App\Entity\Timezone;
use App\Entity\Character;
use App\Entity\CharacterClass;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /** @var UserPasswordEncoderInterface */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        // Users
        $users = [];
        $timezones = $manager->getRepository(Timezone::class)->findBy([], [], 50);
        $classes = $manager->getRepository(CharacterClass::class)->findAll();

        for ($i = 0; $i < 45; $i++) {
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
            $users[] = $user;
        }

        // Characters
        $classes = $manager->getRepository(CharacterClass::class)->findAll();
        $roles = $manager->getRepository(Role::class)->findAll();

        $alliance = $manager->getRepository(Faction::class)->find(1);
        $horde = $manager->getRepository(Faction::class)->find(2);

        $lucifron = $manager->getRepository(Server::class)->find(22);
        $chromie = $manager->getRepository(Server::class)->find(49);
        $zenedar = $manager->getRepository(Server::class)->find(350);

        foreach ($users as $key => $user) {

            $character = new Character();
            $character
                ->setUser($user)
                ->setName('character_' . $key . '_of_' . $user->getName())
                ->setFaction($alliance)
                ->setServer($zenedar)
                ->setCharacterClass($classes[rand(0, 8)])
                ->addRole($roles[rand(0, 2)])
                ->addRole($roles[rand(0, 2)])
                ->addRole($roles[rand(0, 2)])
                ->setInformation('Informations')
                ->setIsArchived(false);

            $manager->persist($character);

            if ($key < 15) {
                $character = new Character();
                $character
                    ->setUser($user)
                    ->setName('character_' . $key . '_of_' . $user->getName())
                    ->setFaction($horde)
                    ->setServer($chromie)
                    ->setCharacterClass($classes[rand(0, 8)])
                    ->addRole($roles[rand(0, 2)])
                    ->addRole($roles[rand(0, 2)])
                    ->addRole($roles[rand(0, 2)])
                    ->setInformation('Informations')
                    ->setIsArchived(false);

                $manager->persist($character);
            }

            if ($key < 30) {
                $character = new Character();
                $character
                    ->setUser($user)
                    ->setName('character_' . $key . '_of_' . $user->getName())
                    ->setFaction($alliance)
                    ->setServer($lucifron)
                    ->setCharacterClass($classes[rand(0, 8)])
                    ->addRole($roles[rand(0, 2)])
                    ->addRole($roles[rand(0, 2)])
                    ->addRole($roles[rand(0, 2)])
                    ->setInformation('Informations')
                    ->setIsArchived(false);

                $manager->persist($character);
            }

            $manager->flush();
        }
    }
}
