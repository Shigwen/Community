<?php

namespace App\Repository;

use App\Entity\RaidCharacter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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

    // /**
    //  * @return RaidCharacter[] Returns an array of RaidCharacter objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?RaidCharacter
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
