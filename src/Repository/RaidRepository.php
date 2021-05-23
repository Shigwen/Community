<?php

namespace App\Repository;

use DateTime;
use App\Entity\Raid;
use App\Entity\RaidCharacter;
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

	/************************
	 *      Raid Leader     *
	 ************************/

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
				'raidLeader' => $raidLeader,
			])
            ->orderBy('r.startAt', 'ASC')
            ->getQuery()
            ->getResult();
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
				'raidLeader' => $raidLeader,
			])
            ->orderBy('r.startAt', 'ASC')
            ->getQuery()
            ->getResult();
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
				'raidLeader' => $raidLeader,
			])
            ->orderBy('r.startAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

	/************************
	 *        Player        *
	 ************************/

	/**
     * @return Raid[]
     */
    public function getPendingRaidsOfPlayer(User $player, $status)
    {
		$now = new DateTime();
		return $this->createQueryBuilder('r')
			->innerJoin('r.raidCharacters', 'rc')
			->join('rc.userCharacter', 'uc')
			->where('uc.user = :player')
            ->andWhere('r.startAt > :now')
			->andWhere('rc.status = :status')
            ->setParameters([
				'now'=> $now,
				'player' => $player->getId(),
				'status' => $status,
			])
            ->orderBy('r.startAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

	/**
     * @return Raid[]
     */
    public function getInProgressRaidsOfPlayer(User $player, $status)
    {
		$now = new DateTime();
		return $this->createQueryBuilder('r')
			->innerJoin('r.raidCharacters', 'rc')
			->join('rc.userCharacter', 'uc')
			->where('uc.user = :player')
			->andWhere('r.startAt > :now')
			->andWhere('r.endAt < :now')
			->andWhere('rc.status = :status')
			->setParameters([
				'now'=> $now,
				'player' => $player->getId(),
				'status' => $status,
			])
			->orderBy('r.startAt', 'ASC')
			->getQuery()
			->getResult();
    }

	/**
     * @return Raid[]
     */
    public function getPastRaidsOfPlayer(User $player, $status)
    {
		$now = new DateTime();
		return $this->createQueryBuilder('r')
			->innerJoin('r.raidCharacters', 'rc')
			->join('rc.userCharacter', 'uc')
			->where('uc.user = :player')
			->andWhere('r.endAt < :now')
			->andWhere('rc.status = :status')
			->setParameters([
				'now'=> $now,
				'player' => $player->getId(),
				'status' => $status,
			])
			->orderBy('r.startAt', 'ASC')
			->getQuery()
			->getResult();
    }

	/**************************************
	 *      Calendar - User logged        *
	 **************************************/

	/**
     * @return Raid[]
     */
    public function getAllRaidWhereUserIsAccepted(User $player)
    {
		$now = new DateTime();
		return  $this->createQueryBuilder('r')
			->join('r.user', 'u')
			->leftJoin('u.blockeds', 'ub')
			->where('ub.id IS NULL OR ub.id != :player')
			->andWhere('r.startAt > :now')
			->andWhere('r.isPrivate = false')
			->setParameters([
				'now'=> $now,
				'player' => $player->getId(),
			])
			->orderBy('r.startAt', 'ASC')
			->getQuery()
			->getResult();
    }

	/**
     * @return Raid[]
     */
    public function getAllRaidWhereUserIsAcceptedFromDate(User $player, DateTime $start)
    {
		$now = new DateTime();
        $start->setTime(
            $now->format('H'),
            $now->format('i'),
            $now->format('s')
        );

		$end = clone $start;
		$end->modify('+1 day')->setTime(23,59);

		return $this->createQueryBuilder('r')
			->join('r.user', 'u')
			->leftJoin('u.blockeds', 'ub')
			->where('ub.id IS NULL OR ub.id != :player')
			->andWhere('r.startAt > :start')
			->andWhere('r.endAt < :end')
			->andWhere('r.isPrivate = false')
			->setParameters([
				'start'=> $start,
				'end'=> $end,
				'player' => $player->getId(),
			])
			->orderBy('r.startAt', 'ASC')
			->getQuery()
			->getResult();
    }

	/************************************
	 *        Calendar - Anonymous      *
	 ************************************/
	
	 /**
     * @return Raid[]
     */
    public function getAllPendingRaid()
    {
		$now = new DateTime();
		return $this->createQueryBuilder('r')
            ->where('r.startAt > :now')
			->andWhere('r.isPrivate = false')
            ->setParameter('now', $now)
            ->orderBy('r.startAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

	/**
     * @return Raid[]
     */
    public function getAllPendingRaidFromDate(DateTime $start)
    {
		$now = new DateTime();
        $start->setTime(
            $now->format('H'),
            $now->format('i'),
            $now->format('s')
        );

		$end = clone $start;
		$end->modify('+1 day')->setTime(23,59);

		return $this->createQueryBuilder('r')
			->andWhere('r.startAt > :start')
			->andWhere('r.endAt < :end')
			->andWhere('r.isPrivate = false')
			->setParameters([
				'start'=> $start,
				'end'=> $end,
			])
            ->orderBy('r.startAt', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
