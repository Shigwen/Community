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
            ->setParameter('user', $user)
            ->orderBy('r.startAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Raid
     */
    public function getRaidTemplateByIdAndUser($raidId, User $user)
    {
        $now = new DateTime();
        return $this->createQueryBuilder('r')
            ->where('r.templateName IS NOT NULL')
            ->andWhere('r.user = :user')
            ->andWhere('r.id = :raidId')
            ->setParameters([
                'raidId' => $raidId,
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
    public function getForthcomingRaidsOfRaidLeader(User $raidLeader)
    {
        $now = new DateTime();
        return $this->createQueryBuilder('r')
            ->where('r.templateName IS NULL')
            ->andWhere('r.startAt > :now')
            ->andWhere('r.user = :raidLeader')
            ->andWhere('r.isArchived = false')
            ->setParameters([
                'now' => $now,
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
            ->andWhere('r.startAt < :now')
            ->andWhere('r.endAt > :now')
            ->andWhere('r.user = :raidLeader')
            ->andWhere('r.isArchived = false')
            ->setParameters([
                'now' => $now,
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
                'now' => $now,
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
    public function getPendingOrWaintingConfirmationRaidsOfCharacter(Character $character)
    {
        $now = new DateTime();
        return $this->createQueryBuilder('r')
            ->innerJoin('r.raidCharacters', 'rc')
            ->where('rc.userCharacter = :character')
            ->andWhere('r.templateName IS NULL')
            ->andWhere('r.startAt > :now')
            ->andWhere('rc.status IN (:status)')
            ->andWhere('r.isArchived = false')
            ->setParameters([
                'now' => $now,
                'character' => $character->getId(),
                'status' => [RaidCharacter::ACCEPT, RaidCharacter::WAITING_CONFIRMATION],
            ])
            ->orderBy('r.startAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Raid[]
     */
    public function getForthcomingRaidsOfPlayer(User $player, $status)
    {
        $now = new DateTime();
        return $this->createQueryBuilder('r')
            ->innerJoin('r.raidCharacters', 'rc')
            ->join('rc.userCharacter', 'uc')
            ->where('uc.user = :player')
            ->andWhere('r.user != :player')
            ->andWhere('r.templateName IS NULL')
            ->andWhere('r.startAt > :now')
            ->andWhere('rc.status = :status')
            ->andWhere('r.isArchived = false')
            ->setParameters([
                'now' => $now,
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
    public function getForthcomingArchivedByRaidLeader(User $player)
    {
        $now = new DateTime();
        return $this->createQueryBuilder('r')
            ->innerJoin('r.raidCharacters', 'rc')
            ->join('rc.userCharacter', 'uc')
            ->where('uc.user = :player')
            ->andWhere('r.user != :player')
            ->andWhere('r.templateName IS NULL')
            ->andWhere('r.startAt > :now')
            ->andWhere('rc.status IN (:status)')
            ->andWhere('r.isArchived = true')
            ->setParameters([
                'now' => $now,
                'player' => $player->getId(),
                'status' => [
                    RaidCharacter::ACCEPT,
                    RaidCharacter::WAITING_CONFIRMATION,
                ],
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
            ->andWhere('r.user != :player')
            ->andWhere('r.templateName IS NULL')
            ->andWhere('r.startAt < :now')
            ->andWhere('r.endAt > :now')
            ->andWhere('rc.status = :status')
            ->andWhere('r.isArchived = false')
            ->setParameters([
                'now' => $now,
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
                'now' => $now,
                'player' => $player->getId(),
                'status' => $status,
            ])
            ->orderBy('r.startAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /************************************
     *             Calendar             *
     ************************************/

    /**
     * @return Raid[]
     */
    public function getAllPendingRaid(User $user = null, Character $character = null, DateTime $start = null, int $nbrOfResultPerPage = null, int $offset = null)
    {
        $now = new DateTime();

        if (!$nbrOfResultPerPage) {
            $qb = $this->createQueryBuilder('raid')
                ->select('count(raid.id)');
        } else {
            $qb = $this->createQueryBuilder('raid');
        }

        $qb
            ->where('raid.startAt > :now')
            ->andWhere('raid.templateName IS NULL')
            ->andWhere('raid.isPrivate = false')
            ->andWhere('raid.isArchived = false')
            ->setParameter('now', $now)
            ->orderBy('raid.startAt', 'ASC');

        if ($user && $character) {
            $qb
                // Filter by user (he must not have been blocked by the raid leader)
                ->join('raid.user', 'raidUser')
                ->leftJoin('raidUser.blockeds', 'userBlocked')
                ->andWhere('userBlocked.id IS NULL OR userBlocked.id != :user')
                ->setParameter('user', $user)
                // Filter using raid leader's character informations (user character must have the same faction / server)
                ->join('raid.raidCharacters', 'raidCharacter')
                ->join('raidCharacter.userCharacter', 'rlCharacter')
                ->andWhere('rlCharacter.user = raidUser.id')
                ->andWhere('rlCharacter.server = :server')
                ->andWhere('rlCharacter.faction = :faction')
                ->setParameter('server', $character->getServer())
                ->setParameter('faction', $character->getFaction());

            // Filter by user character (he must not be subscribed to the raid)
            $subq = $this->createQueryBuilder('r')
                ->select('rc.id')
                ->from(RaidCharacter::class, 'rc')
                ->join('rc.userCharacter', 'uc')
                ->where('rc.raid = raid.id')
                ->andWhere('uc.user = :user');

            $qb->andWhere($qb->expr()->not($qb->expr()->exists($subq->getDQL())));
        }

        if ($start) {
            $end = clone $start;
            $qb
                ->andWhere('raid.startAt BETWEEN :start AND :end')
                ->setParameter('start',  $start->setTime(0, 0, 0))
                ->setParameter('end', $end->setTime(23, 59, 59));
        }

        if (!$nbrOfResultPerPage) {
            return $qb->getQuery()->getSingleScalarResult();
        }

        return $qb
            ->setMaxResults($nbrOfResultPerPage)
            ->setFirstResult($offset)
            ->getQuery()->getResult();
    }
}
