<?php

namespace App\Repository;

use DateTime;
use App\Entity\Raid;
use App\Entity\User;
use App\Entity\Character;
use App\Entity\RaidCharacter;
use App\Entity\Role;
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
    public function getPendingRaidsOfRaidLeader(User $raidLeader)
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
    public function getInProgressRaidsOfPlayer(User $player, $status)
    {
        $now = new DateTime();
        return $this->createQueryBuilder('r')
            ->innerJoin('r.raidCharacters', 'rc')
            ->join('rc.userCharacter', 'uc')
            ->where('uc.user = :player')
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
                'now' => $now,
                'player' => $player->getId(),
            ])
            ->orderBy('r.startAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Raid[]
     */
    public function getAllRaidWhereUserCharacterIsAcceptedFromDate(User $player, Character $character, DateTime $start)
    {
        $end = clone $start;
        $now = new DateTime();

        return $this->createQueryBuilder('raid')
            ->join('raid.user', 'raidUser')
            ->leftJoin('raidUser.blockeds', 'userBlocked')
            ->where('userBlocked.id IS NULL OR userBlocked.id != :player')
            ->join('raid.raidCharacters', 'raidCharacter')
            ->join('raidCharacter.userCharacter', 'character')
            ->andWhere('character.user = raidUser.id')
            ->andWhere('character.server = :server')
            ->andWhere('character.faction = :faction')
            ->andWhere('raid.templateName IS NULL')
            ->andWhere('raid.startAt > :now')
            ->andWhere('raid.startAt BETWEEN :start AND :end')
            ->andWhere('raid.isPrivate = false')
            ->andWhere('raid.isArchived = false')
            ->setParameters([
                'now' => $now,
                'start' => $start->setTime(0, 0, 0),
                'end' => $end->setTime(23, 59, 59),
                'player' => $player,
                'server' => $character->getServer(),
                'faction' => $character->getFaction(),
            ])
            ->orderBy('raid.startAt', 'ASC')
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
        $end = clone $start;

        return $this->createQueryBuilder('r')
            ->where('r.templateName IS NULL')
            ->andWhere('r.startAt BETWEEN :start AND :end')
            ->andWhere('r.isPrivate = false')
            ->andWhere('r.isArchived = false')
            ->setParameters([
                'start' => $start->setTime(0, 0, 0),
                'end' => $end->setTime(23, 59, 59),
            ])
            ->orderBy('r.startAt', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
