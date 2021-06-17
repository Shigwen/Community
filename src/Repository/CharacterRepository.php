<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Server;
use App\Entity\Faction;
use App\Entity\Character;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Character|null find($id, $lockMode = null, $lockVersion = null)
 * @method Character|null findOneBy(array $criteria, array $orderBy = null)
 * @method Character[]    findAll()
 * @method Character[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CharacterRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Character::class);
    }

    /**
     * @return Character[]
     */
    public function getAllByUserAndServerAndFaction(User $user, Server $server, Faction $faction)
    {
        return $this->createQueryBuilder('c')
            ->where('c.user = :user')
            ->andWhere('c.isArchived = 0')
            ->andWhere('c.server = :server')
            ->andWhere('c.faction = :faction')
            ->orderBy('c.name', 'ASC')
            ->setParameters([
                'user' => $user,
                'server' => $server,
                'faction' => $faction
            ])
            ->getQuery()
            ->getResult();;
    }
}
