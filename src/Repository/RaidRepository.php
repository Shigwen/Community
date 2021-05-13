<?php

namespace App\Repository;

use DateTime;
use App\Entity\Raid;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Raid|null find($id, $lockMode = null, $lockVersion = null)
 * @method Raid|null findOneBy(array $criteria, array $orderBy = null)
 * @method Raid[]    findAll()
 * @method Raid[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RaidRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Raid::class);
    }

    /**
     * @return Raid[]
     */
    public function getPendingRaidsOfRaidLeader(User $raidLeader)
    {
		$now = new DateTime();
        return $this->createQueryBuilder('r')
            ->where('r.startAt > :now')
			->andWhere('r.user = :raidLeader')
            ->setParameters([
				'now'=> $now,
				'user' => $raidLeader,
			])
            ->orderBy('r.startAt', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

	/**
     * @return Raid[]
     */
    public function getInProgressRaidsOfRaidLeader(User $raidLeader)
    {
		$now = new DateTime();
        return $this->createQueryBuilder('r')
            ->where('r.startAt > :now')
            ->andWhere('r.endAt < :now')
			->andWhere('r.user = :raidLeader')
            ->setParameters([
				'now'=> $now,
				'user' => $raidLeader,
			])
            ->orderBy('r.startAt', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

	/**
     * @return Raid[]
     */
    public function getPastRaidsOfRaidLeader(User $raidLeader)
    {
		$now = new DateTime();
        return $this->createQueryBuilder('r')
            ->where('r.endAt < :now')
			->andWhere('r.user = :raidLeader')
            ->setParameters([
				'now'=> $now,
				'user' => $raidLeader,
			])
            ->orderBy('r.startAt', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }
}
