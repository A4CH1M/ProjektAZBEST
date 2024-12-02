<?php

namespace App\Repository;

use App\Entity\Room;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Room>
 */
class RoomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Room::class);
    }
    public function findByName(string $name): ?object
    {
        // Znajdź jeden rekord na podstawie nazwy
        return $this->findOneBy(['numer' => $name]);
    }
    public function saveToDb(int $wydzial_id, string $name): ?bool
    {
        $object = $this->findByName($name);
        if ($object){
            return false;
        }
        else {
            $entityManager = $this->getEntityManager();
            $object = new Room();
            $department = $entityManager->getRepository('App:Department')->find($wydzial_id);
            $object->setDepartment($department);
            $object->setNumer($name);

            $entityManager->persist($object);
            $entityManager->flush();
        }
        return true;
    }
    //    /**
    //     * @return Room[] Returns an array of Room objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('s.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Room
    //    {
    //        return $this->createQueryBuilder('s')
    //            ->andWhere('s.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
