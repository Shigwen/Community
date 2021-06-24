<?php

namespace App\DataFixtures;

use DateTime;
use App\Entity\Raid;
use App\Entity\Role;
use App\Entity\User;
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
        // todo pour que ça fonctionne : la base est purgé il faut donc générer aussi les
        // timezones, roles, faction, serveur

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
                ->setLastAttempt(new DateTime())
                ->setCreatedAt(new DateTime());

            $manager->persist($user);
            $users[] = $user;
        }

        // character
        $charactersA = [];
        $factionA = $manager->getRepository(Faction::class)->find(Character::FACTION_ALLIANCE);
        $serverA = $manager->getRepository(CharacterClass::class)->find(1);

        $charactersB = [];
        $factionB = $manager->getRepository(Faction::class)->find(Character::FACTION_HORDE);
        $serverB = $manager->getRepository(CharacterClass::class)->find(2);

        $charactersC = []; // Faction A + Server B
        $charactersD = []; // Faction B + Server A

        $classes = $manager->getRepository(CharacterClass::class)->findAll();
        $roles = $manager->getRepository(Role::class)->findAll();

        foreach ($users as $key => $user) {
            $characterA = new Character();
            $characterA
                ->setUser($user)
                ->setName('character_' . $key . $user->getName())
                ->setFaction($factionA)
                ->setServer($serverA)
                ->setCharacterClass($classes[rand(0, 8)])
                ->setRoles([$roles[rand(0, 2)]])
                ->setInformation('Informations')
                ->setCreatedAt(new DateTime())
                ->setIsArchived(false);

            $manager->persist($characterA);
            $charactersA[] = $characterA;

            if ($key % 2 == 1) {
                $characterB = new Character();
                $characterB
                    ->setUser($user)
                    ->setName('character_' . $key . $user->getName())
                    ->setFaction($factionB)
                    ->setServer($serverB)
                    ->setCharacterClass($classes[rand(0, 8)])
                    ->setRoles([$roles[rand(0, 2)]])
                    ->setInformation('Informations')
                    ->setCreatedAt(new DateTime())
                    ->setIsArchived(false);

                $manager->persist($characterB);
                $charactersB[] = $characterB;
            }

            if ($key < 10) {
                $characterC = new Character();
                $characterC
                    ->setUser($user)
                    ->setName('character_' . $key . $user->getName())
                    ->setFaction($factionA)
                    ->setServer($serverB)
                    ->setCharacterClass($classes[rand(0, 8)])
                    ->setRoles([$roles[rand(0, 2)]])
                    ->setInformation('Informations')
                    ->setCreatedAt(new DateTime())
                    ->setIsArchived(false);

                $manager->persist($characterC);
                $charactersC[] = $characterC;
            }

            if ($key > 10) {
                $characterD = new Character();
                $characterD
                    ->setUser($user)
                    ->setName('character_' . $key . $user->getName())
                    ->setFaction($factionB)
                    ->setServer($serverA)
                    ->setCharacterClass($classes[rand(0, 8)])
                    ->setRoles([$roles[rand(0, 2)]])
                    ->setInformation('Informations')
                    ->setCreatedAt(new DateTime())
                    ->setIsArchived(false);

                $manager->persist($characterD);
                $charactersD[] = $characterD;
            }
        }

        // raid
        $raidsA = [];
        $raidsB = [];
        $raidsC = [];
        $raidsD = [];

        foreach ($charactersA as $key => $character) {
            if ($key % 2 == 1) {
                $startAt = new DateTime(sprintf('-%d days', rand(1, 15)));
                $isPrivate = true;
            } else {
                $startAt = new DateTime(sprintf('+%d days', rand(1, 15)));
                $isPrivate = false;
            }

            $autoAccept = $key > (count($charactersA) / 2) ? true : false;
            $startAt->setTime(20, 0, 0);
            $endAt = clone $startAt;
            $endAt->modify(sprintf('+%d hours', rand(1, 3)));

            $raidA = new Raid();
            $raidA
                ->setUser($character->getUser())
                ->setIdentifier($isPrivate ? $this->identifier->generate(Raid::IDENTIFIER_SIZE) : null)
                ->setName('Raid by ' . $character->getName())
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
                ->setCreatedAt(new DateTime())
                ->setIsArchived(false);

            $raidCharacter = new RaidCharacter();
            $raidCharacter
                ->setRaid($raidA)
                ->setUserCharacter($character)
                ->setRole($roles[rand(0, 2)])
                ->setStatus(RaidCharacter::ACCEPT);

            $manager->persist($raidA);
            $manager->persist($raidCharacter);
            $raidsA[] = $raidA;
        }

        foreach ($charactersB as $key => $character) {
            if ($key % 2 == 1) {
                $startAt = new DateTime(sprintf('-%d days', rand(1, 15)));
                $isPrivate = true;
            } else {
                $startAt = new DateTime(sprintf('+%d days', rand(1, 15)));
                $isPrivate = false;
            }

            $autoAccept = $key > (count($charactersA) / 2) ? true : false;
            $startAt->setTime(20, 0, 0);
            $endAt = clone $startAt;
            $endAt->modify(sprintf('+%d hours', rand(1, 3)));

            $raidB = new Raid();
            $raidB
                ->setUser($character->getUser())
                ->setIdentifier($isPrivate ? $this->identifier->generate(Raid::IDENTIFIER_SIZE) : null)
                ->setName('Raid by ' . $character->getName())
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
                ->setCreatedAt(new DateTime())
                ->setIsArchived(false);

            $raidCharacter = new RaidCharacter();
            $raidCharacter
                ->setRaid($raidB)
                ->setUserCharacter($character)
                ->setRole($roles[rand(0, 2)])
                ->setStatus(RaidCharacter::ACCEPT);

            $manager->persist($raidB);
            $manager->persist($raidCharacter);
            $raidsB[] = $raidB;
        }

        foreach ($charactersC as $key => $character) {
            if ($key % 2 == 1) {
                $startAt = new DateTime(sprintf('-%d days', rand(1, 15)));
                $isPrivate = true;
            } else {
                $startAt = new DateTime(sprintf('+%d days', rand(1, 15)));
                $isPrivate = false;
            }

            $autoAccept = $key > (count($charactersA) / 2) ? true : false;
            $startAt->setTime(20, 0, 0);
            $endAt = clone $startAt;
            $endAt->modify(sprintf('+%d hours', rand(1, 3)));

            $raidC = new Raid();
            $raidC
                ->setUser($character->getUser())
                ->setIdentifier($isPrivate ? $this->identifier->generate(Raid::IDENTIFIER_SIZE) : null)
                ->setName('Raid by ' . $character->getName())
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
                ->setCreatedAt(new DateTime())
                ->setIsArchived(false);

            $raidCharacter = new RaidCharacter();
            $raidCharacter
                ->setRaid($raidC)
                ->setUserCharacter($character)
                ->setRole($roles[rand(0, 2)])
                ->setStatus(RaidCharacter::ACCEPT);

            $manager->persist($raidC);
            $manager->persist($raidCharacter);
            $raidsC[] = $raidC;
        }

        foreach ($charactersD as $key => $character) {
            if ($key % 2 == 1) {
                $startAt = new DateTime(sprintf('-%d days', rand(1, 15)));
                $isPrivate = true;
            } else {
                $startAt = new DateTime(sprintf('+%d days', rand(1, 15)));
                $isPrivate = false;
            }

            $autoAccept = $key > (count($charactersA) / 2) ? true : false;
            $startAt->setTime(20, 0, 0);
            $endAt = clone $startAt;
            $endAt->modify(sprintf('+%d hours', rand(1, 3)));

            $raidD = new Raid();
            $raidD
                ->setUser($character->getUser())
                ->setIdentifier($isPrivate ? $this->identifier->generate(Raid::IDENTIFIER_SIZE) : null)
                ->setName('Raid by ' . $character->getName())
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
                ->setCreatedAt(new DateTime())
                ->setIsArchived(false);

            $raidCharacter = new RaidCharacter();
            $raidCharacter
                ->setRaid($raidD)
                ->setUserCharacter($character)
                ->setRole($roles[rand(0, 2)])
                ->setStatus(RaidCharacter::ACCEPT);

            $manager->persist($raidD);
            $manager->persist($raidCharacter);
            $raidsD[] = $raidD;
        }

        // todo : character subscribe raid

        $manager->flush();
    }
}
