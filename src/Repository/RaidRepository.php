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

	/************************
	 *       Templates      *
	 ************************/
    /**
     * @return Raid[]
     */
    public function getRaidTemplateByUser(User $user)
    {
        return $this->createQueryBuilder('r')
            ->where('r.templateName IS NOT NULL')
			->andWhere('r.user = :user')
            ->setParameter('user' , $user)
            ->orderBy('r.startAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

	/**
     * @return Raid
     */
    public function getRaidTemplateByIdAndUser( $raidId, User $user)
    {
		$now = new DateTime();
        return $this->createQueryBuilder('r')
            ->where('r.templateName IS NOT NULL')
			->andWhere('r.user = :user')
			->andWhere('r.id = :raidId')
			->setParameters([
				'raidId'=> $raidId,
				'user' => $user,
			])
            ->orderBy('r.startAt', 'ASC')
            ->getQuery()
            ->getOneOrNullResult();
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
			->where('r.templateName IS NULL')
            ->andWhere('r.startAt > :now')
			->andWhere('r.user = :raidLeader')
			->andWhere('r.isArchived = false')
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
			->where('r.templateName IS NULL')
            ->andWhere('r.startAt > :now')
            ->andWhere('r.endAt < :now')
			->andWhere('r.user = :raidLeader')
			->andWhere('r.isArchived = false')
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
			->where('r.templateName IS NULL')
            ->andWhere('r.endAt < :now')
			->andWhere('r.user = :raidLeader')
			->andWhere('r.isArchived = false')
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
			->andWhere('r.templateName IS NULL')
            ->andWhere('r.startAt > :now')
			->andWhere('rc.status = :status')
			->andWhere('r.isArchived = false')
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
			->andWhere('r.templateName IS NULL')
			->andWhere('r.startAt > :now')
			->andWhere('r.endAt < :now')
			->andWhere('rc.status = :status')
			->andWhere('r.isArchived = false')
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
			->andWhere('r.templateName IS NULL')
			->andWhere('r.endAt < :now')
			->andWhere('rc.status = :status')
			->andWhere('r.isArchived = false')
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
			->andWhere('r.templateName IS NULL')
			->andWhere('r.startAt > :now')
			->andWhere('r.isPrivate = false')
			->andWhere('r.isArchived = false')
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
			->andWhere('r.templateName IS NULL')
			->andWhere('r.startAt > :start')
			->andWhere('r.endAt < :end')
			->andWhere('r.isPrivate = false')
			->andWhere('r.isArchived = false')
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
			->andWhere('r.templateName IS NULL')
			->andWhere('r.isPrivate = false')
			->andWhere('r.isArchived = false')
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
			->where('r.templateName IS NULL')
			->andWhere('r.startAt > :start')
			->andWhere('r.endAt < :end')
			->andWhere('r.isPrivate = false')
			->andWhere('r.isArchived = false')
			->setParameters([
				'start'=> $start,
				'end'=> $end,
			])
            ->orderBy('r.startAt', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
