<?php

namespace App\Repository;

use App\Entity\Raid;
use App\Entity\User;
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
    public function getAllNotRefusedFromRaid(Raid $raid)
    {
        return $this->createQueryBuilder('rc')
            ->join('rc.raid', 'r')
            ->where('rc.raid = :raid')
            ->andWhere('rc.status != :refused')
            ->setParameters([
                'raid' => $raid,
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
