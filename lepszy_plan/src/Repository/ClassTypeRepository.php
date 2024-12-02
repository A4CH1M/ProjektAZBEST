<?php

namespace App\Repository;

use App\Entity\ClassType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ClassType>
 */
class ClassTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ClassType::class);
    }

    public function findByName(string $name): ?object
    {
        // Znajdź jeden rekord na podstawie nazwy
        return $this->findOneBy(['typ' => $name]);
    }
    public function saveToDb(string $name): ?bool
    {
        $typ = $this->findByName($name);
        if ($typ){
            return false;
        }
        else {
            $typ = new ClassType();
            $typ->setTyp($name);

            $entityManager = $this->getEntityManager();
            $entityManager->persist($typ);
            $entityManager->flush();
        }
        return true;
    }

    //    /**
    //     * @return ClassType[] Returns an array of ClassType objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?ClassType
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
