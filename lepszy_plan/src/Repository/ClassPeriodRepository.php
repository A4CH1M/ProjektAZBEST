<?php

namespace App\Repository;

use App\Entity\ClassPeriod;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ClassPeriod>
 */
class ClassPeriodRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ClassPeriod::class);
    }

    public function findSubjectsByIndex(int $studentIndex): array
    {
        return $this->createQueryBuilder('cp')
            ->join('cp.subject', 'sub')
            ->join('App\Entity\GroupStudent', 'gs', 'WITH', 'gs.group = cp.group')
            ->join('App\Entity\Student', 's', 'WITH', 's.id = gs.student')
            ->where('s.studentIndex = :studentIndex') // Filtrujemy po ID studenta
            ->setParameter('studentIndex', $studentIndex)
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return ClassPeriod[] Returns an array of ClassPeriod objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('b.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?ClassPeriod
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
