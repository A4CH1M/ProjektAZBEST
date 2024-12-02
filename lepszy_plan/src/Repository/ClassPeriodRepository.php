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

    public function findByDataRozpoczecia(\DateTimeInterface $dateTime): ?object
    {
        // Znajdź jeden rekord na podstawie nazwy
        return $this->findOneBy(['data_rozpoczecia' => $dateTime]);
    }
    public function saveToDb(int $grupa_id,
                             int $prowadzacy_id,
                             int $sala_id,
                             int $typ_zajec_id,
                             int $przedmiot_id,
                             \DateTimeInterface $data_rozpoczecia,
                             \DateTimeInterface $data_zakonczenia
    ): ?bool
    {
        $object = $this->findByDataRozpoczecia($data_rozpoczecia);
        if ($object) {
            return false;
        }
        else {
            $entityManager = $this->getEntityManager();
            $object = new ClassPeriod();

            $group = $entityManager->getRepository('App:Group')->find($grupa_id);
            $object->setGroup($group);
            $teacher = $entityManager->getRepository('App:Teacher')->find($prowadzacy_id);
            $object->setTeacher($teacher);
            $room = $entityManager->getRepository('App:Room')->find($sala_id);
            $object->setRoom($room);
            $classType = $entityManager->getRepository('App:ClassType')->find($typ_zajec_id);
            $object->setGroup($classType);
            $subject = $entityManager->getRepository('App:Subject')->find($przedmiot_id);
            $object->setSubject($subject);

            $object->setData_rozpoczecia($data_rozpoczecia);
            $object->setData_zakonczenia($data_zakonczenia);

            $entityManager->persist($object);
            $entityManager->flush();
        }
        return true;
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
