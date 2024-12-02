<?php

namespace App\Repository;

use App\Entity\Teacher;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Teacher>
 */
class TeacherRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $entityManager;
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Teacher::class);
        $this->entityManager = $entityManager;
    }
    public function findByName(string $name): ?object
    {
        // Znajdź jeden rekord na podstawie nazwy
        return $this->findOneBy(['imienazwisko' => $name]);
    }
    public function fetchAndSaveTeachers()
    {
        // Użycie file_get_contents do pobrania JSON z API
        $url = 'https://plan.zut.edu.pl/schedule.php?kind=teacher&query=';
        $json = file_get_contents($url);

        // Dekodowanie JSON do tablicy PHP
        $data = json_decode($json, true);

        // Sprawdzenie, czy dane zostały poprawnie zdekodowane
        if ($data === null) {
            throw new \Exception("Błąd podczas dekodowania JSON.");
        }

        // Załóżmy, że dane są w formacie [{"item": "Abramek Karol"}, {...}, ...]
        $teachers = [];
        foreach ($data as $entry) {
            // Sprawdzenie, czy istnieje pole 'item'
            if (isset($entry['item'])) {
                $teachers[] = $entry['item'];
            }
        }

        // Zapisywanie nauczycieli do bazy danych
        foreach ($teachers as $teacherName) {
            echo $teacherName . "\r\n";
            $teacher = new Teacher();
            $teacher->setImienazwisko($teacherName);

            $this->entityManager->persist($teacher);
        }

        // Zatwierdzenie zmian w bazie danych, odkomentowac gdy potrzebne
        //$this->entityManager->flush();
    }


    //    /**
    //     * @return Teacher[] Returns an array of Teacher objects
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

    //    public function findOneBySomeField($value): ?Teacher
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
