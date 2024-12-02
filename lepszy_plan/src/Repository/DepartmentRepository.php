<?php

namespace App\Repository;

use App\Entity\Department;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Department>
 */
class DepartmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Department::class);
    }

    public function findByName(string $name): ?object
    {
        // Znajdź jeden rekord na podstawie nazwy
        return $this->findOneBy(['nazwa' => $name]);
    }
    public function saveToDb(string $name): ?bool
    {
        $department = $this->findByName($name);
        if ($department){
            return false;
        }
        else {
            $department = new Department();
            $department->setNazwa($name);

            $entityManager = $this->getEntityManager();
            $entityManager->persist($department);
            $entityManager->flush();
        }
        return true;
    }

    //    /**
    //     * @return Department[] Returns an array of Department objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('w')
    //            ->andWhere('w.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('w.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Department
    //    {
    //        return $this->createQueryBuilder('w')
    //            ->andWhere('w.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
