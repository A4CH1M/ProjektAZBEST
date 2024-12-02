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

    public function findByGroupId(int $id): ?object
    {
        // Znajdź jeden rekord na podstawie nazwy
        return $this->findOneBy(['grupa_id' => $id]);
    }
    public function findByStudentId(int $id): ?object
    {
        // Znajdź jeden rekord na podstawie nazwy
        return $this->findOneBy(['student_id' => $id]);
    }
    public function saveToDb(int $student_id, int $group_id): ?bool
    {
        $object = $this->findByStudentId($student_id);
        if ($object) {
            return false;
        }
        else {
            $entityManager = $this->getEntityManager();
            $object = new GroupStudent();
            $student = $entityManager->getRepository('App:Student')->find($student_id);
            $object->setStudent($student);
            $group = $entityManager->getRepository('App:Group')->find($group_id);
            $object->setGroup($group);

            $entityManager->persist($object);
            $entityManager->flush();
        }
        return true;
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
