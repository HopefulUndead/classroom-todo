<?php

namespace App\Repository;

use App\Entity\Classroom;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Classroom>
 */
class ClassroomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Classroom::class);
    }
    public function findByUser(int $userId): array
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('App\Entity\UserClassrom', 'uc', 'WITH', 'uc.idClassroom = c.id')
            ->andWhere('uc.idUser = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }

        //    /**
    //     * @return Classroom[] Returns an array of Classroom objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }
    //    public function findOneBySomeField($value): ?Classroom
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
