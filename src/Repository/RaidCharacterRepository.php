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
     * @return RaidCharacter[] Returns an array of RaidCharacter objects
     */
    public function userAlreadyRegisterInRaid(User $user, Raid $raid)
    {
        return $this->createQueryBuilder('rc')
			->join('rc.userCharacter', 'uc')
			->join('rc.raid', 'r')
            ->andWhere('rc.raid = :raid')
            ->andWhere('uc.user = :user')
            ->setParameters([
				'raid' => $raid,
				'user' => $user,
			])
			->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

}
