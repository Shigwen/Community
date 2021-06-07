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
}
