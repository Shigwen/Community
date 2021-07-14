<?php

namespace App\Repository;

use DateTime;
use App\Entity\Raid;
use App\Entity\User;
use App\Entity\Character;
use App\Entity\RaidCharacter;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method RaidCharacter|null find($id, $lockMode = null, $lockVersion = null)
 * @method RaidCharacter|null findOneBy(array $criteria, array $orderBy = null)
 * @method RaidCharacter[]    findAll()
 * @method RaidCharacter[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RaidCharacterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RaidCharacter::class);
    }

    /**
     * @return RaidCharacter[]
     */
    public function getAllOfUser(User $user)
    {
        return $this->createQueryBuilder('rc')
            ->join('rc.userCharacter', 'uc')
            ->where('uc.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return RaidCharacter
     */
    public function getOfRaidLeaderFromRaid(Raid $raid)
    {
        return $this->createQueryBuilder('rc')
            ->join('rc.raid', 'r')
            ->join('rc.userCharacter', 'uc')
            ->where('rc.raid = :raid')
            ->andWhere('uc.user = r.user')
            ->setParameter('raid', $raid)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return RaidCharacter
     */
    public function getOfUserFromRaid(Raid $raid, User $user)
    {
        return $this->createQueryBuilder('rc')
            ->join('rc.raid', 'r')
            ->join('rc.userCharacter', 'uc')
            ->where('rc.raid = :raid')
            ->andWhere('uc.user = :user')
            ->setParameters([
                'raid' => $raid,
                'user' => $user,
            ])
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return RaidCharacter[]
     */
    public function getAllFutureRaidsNotRefusedWithCharacter(Character $character)
    {
        $now = new DateTime();
        return $this->createQueryBuilder('rc')
            ->join('rc.raid', 'r')
            ->where('rc.userCharacter = :character')
            ->andWhere('r.startAt > :now')
            ->andWhere('rc.status != :refused')
            ->setParameters([
                'now' => $now,
                'character' => $character,
                'refused' => RaidCharacter::REFUSED,
            ])
            ->getQuery()
            ->getResult();
    }

    /**
     * @return RaidCharacter[]
     */
    public function getAllWithRole(Raid $raid, int $role)
    {
        return $this->createQueryBuilder('rc')
            ->join('rc.raid', 'r')
            ->where('rc.raid = :raid')
            ->andWhere('rc.status = :accept')
            ->andWhere('rc.role = :role')
            ->setParameters([
                'raid' => $raid,
                'accept' => RaidCharacter::ACCEPT,
                'role' => $role,
            ])
            ->getQuery()
            ->getResult();
    }
}
