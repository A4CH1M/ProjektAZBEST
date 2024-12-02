<?php

namespace App\Repository;

use App\Entity\Subject;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Subject>
 */
class SubjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subject::class);
    }

    public function findByName(string $name): ?object
    {
        // Znajdź jeden rekord na podstawie nazwy
        return $this->findOneBy(['nazwa' => $name]);
    }
    public function saveToDb(string $name): ?bool
    {
        $subject = $this->findByName($name);
        if ($subject){
            return false;
        }
        else {
            $subject = new Subject();
            $subject->setNazwa($name);

            $entityManager = $this->getEntityManager();
            $entityManager->persist($subject);
            $entityManager->flush();
        }
        return true;
    }

    //    /**
    //     * @return Subject[] Returns an array of Subject objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Subject
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
