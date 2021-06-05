<?php

namespace App\Repository;

use App\Entity\MessageType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method MessageType|null find($id, $lockMode = null, $lockVersion = null)
 * @method MessageType|null findOneBy(array $criteria, array $orderBy = null)
 * @method MessageType[]    findAll()
 * @method MessageType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MessageType::class);
    }
}
