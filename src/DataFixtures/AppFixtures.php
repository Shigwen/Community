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
        $users = [];
        $timezones = $manager->getRepository(Timezone::class)->findBy([], [], 50);

        for ($i = 0; $i <= 20; $i++) {
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

        $characters = [];
        $faction = $manager->getRepository(Faction::class)->find(1);
        $server = $manager->getRepository(Server::class)->find(1);
        $classes = $manager->getRepository(CharacterClass::class)->findAll();
        $roles = $manager->getRepository(Role::class)->findAll();

        foreach ($users as $key => $user) {
            $character = new Character();
            $character
                ->setUser($user)
                ->setName('character_' . $key)
                ->setFaction($faction)
                ->setServer($server)
                ->setCharacterClass($classes[rand(0, 8)])
                ->addRole($roles[rand(0, 2)])
                ->addRole($roles[rand(0, 2)])
                ->addRole($roles[rand(0, 2)])
                ->setInformation('Informations')
                ->setIsArchived(false);

            $manager->persist($character);
            $characters[] = $character;
        }

        foreach ($characters as $key => $character) {

            $isPrivate = false;
            $future = false;

            if ($key % 5 == 1) {
                $isPrivate = true;
            }

            $autoAccept = $key > (count($characters) / 2) ? true : false;

            $raid = new Raid();
            if ($key % 2 == 1) {
                $startAt = new DateTime(sprintf('-%d days', rand(1, 15)));
            } else {
                $startAt = new DateTime(sprintf('+%d days', rand(1, 15)));
                $future = true;
            }
            $startAt->setTime(20, 0, 0);
            $endAt = clone $startAt;
            $endAt->modify(sprintf('+%d hours', rand(1, 3)));

            $raid
                ->setUser($character->getUser())
                ->setIdentifier($isPrivate ? $this->identifier->generate(Raid::IDENTIFIER_SIZE) : null)
                ->setName('Raid by ' . $character->getUser()->getName())
                ->setRaidType(25)
                ->setExpectedAttendee(24)
                ->setStartAt($startAt)
                ->setEndAt($endAt)
                ->setInformation('Informations')
                ->setMinTank(rand(1, 3))
                ->setMaxTank(rand(4, 5))
                ->setMinHeal(rand(1, 3))
                ->setMaxHeal(rand(4, 5))
                ->setIsPrivate($isPrivate)
                ->setAutoAccept($autoAccept)
                ->setIsArchived(false);

            if ($future) {
                foreach ($characters as $character) {
                    $raidCharacter = new RaidCharacter();
                    $raidCharacter
                        ->setRaid($raid)
                        ->setUserCharacter($character)
                        ->setRole($roles[rand(0, 2)])
                        ->setStatus($raid->isAutoAccept());

                    $manager->persist($raidCharacter);
                }
            } else {
                $raidCharacter = new RaidCharacter();
                $raidCharacter
                    ->setRaid($raid)
                    ->setUserCharacter($character)
                    ->setRole($roles[rand(0, 2)])
                    ->setStatus(RaidCharacter::ACCEPT);
                $manager->persist($raidCharacter);
            }

            $manager->persist($raid);
        }

        $manager->flush();
    }
}
