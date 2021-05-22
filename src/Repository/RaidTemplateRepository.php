<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\RaidTemplate;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method RaidTemplate|null find($id, $lockMode = null, $lockVersion = null)
 * @method RaidTemplate|null findOneBy(array $criteria, array $orderBy = null)
 * @method RaidTemplate[]    findAll()
 * @method RaidTemplate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RaidTemplateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RaidTemplate::class);
    }

    /**
     * @return RaidTemplate
     */
    public function findByIdAnduser($id, User $user)
    {
        return $this->createQueryBuilder('rt')
            ->where('rt.id = :id')
			->andWhere('rt.user = :user')
            ->setParameters([
				'id' => $id,
				'user' => $user
			])
            ->getQuery()
            ->getOneOrNullResult();
    }
}
