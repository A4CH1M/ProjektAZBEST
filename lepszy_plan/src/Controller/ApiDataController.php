<?php

namespace App\Controller;

use App\Entity\Department;
use App\Entity\Room;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Teacher;

class ApiDataController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function teacherDownloadAction(): Response
    {
        $url = 'https://plan.zut.edu.pl/schedule.php?kind=teacher&query=';
        $json = file_get_contents($url);

        $data = json_decode($json, true);

        if ($data === null) {
            throw new \Exception("Błąd podczas dekodowania JSON.");
        }

        $teachers = [];
        foreach ($data as $entry) {
            if (isset($entry['item'])) {
                $teachers[] = $entry['item'];
            }
        }

        foreach ($teachers as $teacherName) {
            $teacher = new Teacher();
            $teacher->setFullName($teacherName);

            $this->entityManager->persist($teacher);
        }

        $this->entityManager->flush();

        return new Response('Downloaded');
    }

    public function roomDownloadAction(): Response
    {
        $url = 'https://plan.zut.edu.pl/schedule.php?kind=room&query=';
        $json = file_get_contents($url);

        $data = json_decode($json, true);

        if ($data === null) {
            throw new \Exception("Błąd podczas dekodowania JSON.");
        }

        $departments = [];
        foreach ($data as $entry) {
            if (isset($entry['item'])) {
                if (str_contains($entry['item'], ' ')) {
                    $parts = explode(' ', $entry['item'], 2);
                }
                elseif (str_contains($entry['item'], '_')){
                    $parts = explode('_', $entry['item'], 2);
                }
                else {
                    continue; // te dwie sale nie istnieją: TeatrTEATR, WBiIŚ064
                }

                $departmentName = $parts[0];
                $roomNumber = $parts[1];

                if (!isset($departments[$departmentName])) {
                    $department = new Department();
                    $department->setName($departmentName);
                    $this->entityManager->persist($department);

                    $departments[$departmentName] = $department;
                } else {
                    $department = $departments[$departmentName];
                }

                $room = new Room();
                $room->setNumber($roomNumber);
                $room->setDepartment($department);
                $this->entityManager->persist($room);
            }
        }

        $this->entityManager->flush();

        return new Response('Downloaded');
    }

}