<?php

namespace App\Service;

use App\Entity\ClassGroup;
use App\Entity\ClassPeriod;
use App\Entity\ClassType;
use App\Entity\Department;
use App\Entity\GroupStudent;
use App\Entity\Room;
use App\Entity\Student;
use App\Entity\Subject;
use App\Entity\Teacher;
use App\Repository\ClassGroupRepository;
use App\Repository\RoomRepository;
use App\Repository\SubjectRepository;
use App\Repository\TeacherRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\Response;
use function PHPUnit\Framework\assertArrayHasKey;

class ApiDataManager
{
    private readonly EntityManagerInterface $entityManager;

    private readonly TeacherRepository $teacherRepository;
    private readonly RoomRepository $roomRepository;
    private readonly ClassGroupRepository $classGroupRepository;
    private readonly SubjectRepository $subjectRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        TeacherRepository $teacherRepository,
        RoomRepository $roomRepository,
        ClassGroupRepository $classGroupRepository,
        SubjectRepository $subjectRepository
    )
    {
        $this->entityManager = $entityManager;
        $this->teacherRepository = $teacherRepository;
        $this->roomRepository = $roomRepository;
        $this->classGroupRepository = $classGroupRepository;
        $this->subjectRepository = $subjectRepository;
    }


    public function teacherDownload(): Response
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

    public function roomDownload(): Response
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

    public function subjectDownload(): Response
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

    public function groupDownload(): Response
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

    public function classPeriodDownload(): Response
    {
        ini_set("memory_limit", "-1");
        set_time_limit(3600);

        $classTypes = [];
        $classPeriods = [];
        $newGroups = [];

        for ($i = 51000; $i < 51050; $i++) {
            $url = 'https://plan.zut.edu.pl/schedule_student.php?number=' . $i . '&start=2024-12-02T00%3A00%3A00%2B01%3A00&end=2024-12-09T00%3A00%3A00%2B01%3A00';
            $json = file_get_contents($url);

            if(!str_contains($json, ',')) {
                echo $i . PHP_EOL;
                continue;
            }

            $parts = explode(',', $json, 2);
            $json = '[' . $parts[1];
            $data = json_decode($json, true);

            if ($data === null) {
                throw new \Exception("Błąd podczas dekodowania JSON.");
            }

            if (empty($data)) {
                continue;
            }

            $student = new Student();
            $student->setStudentIndex($i);
            $this->entityManager->persist($student);

            $groups = [];
            foreach ($data as $entry) {
                $groupNumber = $entry['group_name'];
                $group = null;

                if (!in_array($groupNumber, $groups)) {
                    $groups[] = $groupNumber;
                    $group = $this->classGroupRepository->findOneBy(['number' => $groupNumber]);

                    if (!$group) {
                        $group = new ClassGroup();
                        $group->setNumber($groupNumber);
                        $newGroups[$groupNumber] = $group;
                        $this->entityManager->persist($group);
                    }

                    $groupStudent = new GroupStudent();
                    $groupStudent->setStudent($student);
                    $groupStudent->setGroup($group);
                    $this->entityManager->persist($groupStudent);
                }

                $classTypeName = $entry['lesson_form'];
                $classType = null;
                if (!array_key_exists($classTypeName, $classTypes)) {
                    //add check if already in db
                    $classType = new ClassType();
                    $classType->setType($classTypeName);
                    $classTypes[$classTypeName] = $classType;
                    $this->entityManager->persist($classType);
                }

                $classPeriod = new ClassPeriod();

                if (!$group) {
                    $group = $this->classGroupRepository->findOneBy(['number' => $groupNumber]);
                    if (!$group) {
                        $group = $newGroups[$groupNumber];
                    }
                }
                $classPeriod->setgroup($group);

                if(!$classType){
                    $classType = $classTypes[$classTypeName];
                }
                $classPeriod->setClassType($classType);

                $teacherName = $entry['worker'];
                $teacher = $this->teacherRepository->findOneBy(['fullName' => $teacherName]);
                $classPeriod->setTeacher($teacher);

                if (str_contains($entry['room'], ' ')) {
                    $roomName = explode(' ', $entry['room'], 2)[1];
                }
                else {
                    $roomName = explode('_', $entry['room'], 2)[1];
                }
                $room = $this->roomRepository->findOneBy(['number' => $roomName]);
                $classPeriod->setRoom($room);

                $subjectName = mb_convert_case($entry['subject'], MB_CASE_TITLE, "UTF-8");
                $subject = $this->subjectRepository->findOneBy(['name' => $subjectName]);
                if (!$subject) {
                    echo "'".$subjectName."'" . PHP_EOL;
                }
                $classPeriod->setSubject($subject);

                $dateStart = new \DateTime($entry['start']);
                $classPeriod->setDatetimeStart($dateStart);
                $dateEnd = new \DateTime($entry['end']);
                $classPeriod->setDatetimeStop($dateEnd);

                $classKey = $teacherName . $dateStart->format('Y m d H i s') . $dateEnd->format('Y m d H i s');

                if(!array_key_exists($classKey, $classPeriods)) {
                    $classPeriods[$classKey] = $classPeriod;
                    $this->entityManager->persist($classPeriod);
                }
            }

            $this->entityManager->flush();
        }

        return new Response('Downloaded');
    }
}