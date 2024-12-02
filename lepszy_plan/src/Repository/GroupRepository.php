<?php

namespace App\Repository;

use App\Entity\Group;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Group>
 */
class GroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Group::class);
    }

    public function findByName(string $name): ?object
    {
        // Znajdź jeden rekord na podstawie nazwy
        return $this->findOneBy(['numer' => $name]);
    }
    public function saveToDb(string $name): ?bool
    {
        $group = $this->findByName($name);
        if ($group){
            return false;
        }
        else {
            $group = new Group();
            $group->setNumer($name);

            $entityManager = $this->getEntityManager();
            $entityManager->persist($group);
            $entityManager->flush();
        }
        return true;
    }

    //    /**
    //     * @return Group[] Returns an array of Group objects
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

    //    public function findOneBySomeField($value): ?Group
    //    {
    //        return $this->createQueryBuilder('g')
    //            ->andWhere('g.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
