<?php

namespace App\Controller;

use App\Entity\Department;
use App\Entity\ClassGroup;
use App\Entity\Room;
use App\Entity\Subject;
use App\Repository\ClassGroupRepository;
use App\Repository\RoomRepository;
use App\Repository\TeacherRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Teacher;

class ApiDataController extends AbstractController
{
    private readonly EntityManagerInterface $entityManager;

    private readonly TeacherRepository $teacherRepository;
    private readonly RoomRepository $roomRepository;
    private readonly ClassGroupRepository $classGroupRepository;

    public function __construct(EntityManagerInterface $entityManager,
                                TeacherRepository $teacherRepository,
                                RoomRepository $roomRepository,
                                ClassGroupRepository $classGroupRepository
    )
    {
        $this->entityManager = $entityManager;
        $this->teacherRepository = $teacherRepository;
        $this->roomRepository = $roomRepository;
        $this->classGroupRepository = $classGroupRepository;
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
        $rooms = [];
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

                if (!isset($rooms[$roomNumber])) {

                    $room = new Room();
                    $room->setNumber($roomNumber);
                    $room->setDepartment($department);
                    $this->entityManager->persist($room);

                    $rooms[$roomNumber] = $room;
                }
            }
        }

        $this->entityManager->flush();

        return new Response('Downloaded');
    }

    public function subjectDownloadAction(): Response
    {
        $url = 'https://plan.zut.edu.pl/schedule.php?kind=subject&query=';
        $json = file_get_contents($url);

        $data = json_decode($json, true);

        if ($data === null) {
            throw new \Exception("Błąd podczas dekodowania JSON.");
        }

        $subjects = [];
        foreach ($data as $entry) {
            $fullString = $entry['item'];

            if (!isset($fullString)) {
                continue;
            }

            $position = strpos($fullString, '(');
            $sub = substr($fullString, 0, $position - 1);

            if (!in_array($sub, $subjects)) {
                $subjects[] = $sub;

                # echo "<pre>" . $sub . PHP_EOL . "</pre>";

                $subject = new Subject();
                $subject->setName($sub);

                $this->entityManager->persist($subject);
            }
        }

        $this->entityManager->flush();

        return new Response('Downloaded');
    }

    public function groupDownloadAction(): Response
    {
        ini_set("memory_limit", "-1");

        $url = 'https://plan.zut.edu.pl/schedule.php?kind=group';
        $json = file_get_contents($url);

        $kaboom = preg_split("/\\r\\n|\\r|\\n/", $json);
        $json = end($kaboom);

        $data = json_decode($json, true);

        if ($data === null) {
            throw new \Exception("Błąd podczas dekodowania JSON.");
        }

        $groups = [];

        foreach ($data as $entry) {
            $groupName = $entry['item'];

            if (!isset($groupName)) {
                continue;
            }

            if (!in_array($groupName, $groups)) {
                $groups[] = $groupName;

                $group = new ClassGroup();
                $group->setNumber($groupName);

                $this->entityManager->persist($group);
            }
        }

        $this->entityManager->flush();

        return new Response('Downloaded');
    }

}