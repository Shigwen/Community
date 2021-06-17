<?php

namespace App\Repository;

use App\Entity\Ip;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Ip|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ip|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ip[]    findAll()
 * @method Ip[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IpRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ip::class);
    }
}
