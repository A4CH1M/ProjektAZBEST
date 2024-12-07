<?php

namespace App\Repository;

use App\Entity\GroupStudent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GroupStudent>
 */
class GroupStudentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GroupStudent::class);
    }

    public function findGroupsByStudentId(int $studentIndex): array
    {
        return $this->createQueryBuilder('gs')
            ->select('cg.number') // Wybieramy dane grupy
            ->join('gs.student', 's') // Łączymy z tabelą student
            ->join('gs.group','cg')
            ->where('s.studentIndex = :studentIndex') // Filtrujemy po ID studenta
            ->setParameter('studentIndex', $studentIndex)
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return GroupStudent[] Returns an array of GroupStudent objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('g')
    //            ->andWhere('g.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('g.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?GroupStudent
    //    {
    //        return $this->createQueryBuilder('g')
    //            ->andWhere('g.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
