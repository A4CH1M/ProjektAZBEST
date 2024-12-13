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
use App\Repository\ClassPeriodRepository;
use App\Repository\ClassTypeRepository;
use App\Repository\DepartmentRepository;
use App\Repository\GroupStudentRepository;
use App\Repository\RoomRepository;
use App\Repository\StudentRepository;
use App\Repository\SubjectRepository;
use App\Repository\TeacherRepository;
use Doctrine\ORM\EntityManagerInterface;
use mysql_xdevapi\Exception;
use PHPUnit\TextUI\XmlConfiguration\Group;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Yaml;
use function PHPUnit\Framework\assertArrayHasKey;

class ApiDataManager
{
    private readonly EntityManagerInterface $entityManager;

    private readonly TeacherRepository $teacherRepository;
    private readonly RoomRepository $roomRepository;
    private readonly ClassGroupRepository $classGroupRepository;
    private readonly SubjectRepository $subjectRepository;
    private readonly DepartmentRepository $departmentRepository;
    private readonly StudentRepository $studentRepository;
    private readonly ClassPeriodRepository $classPeriodRepository;
    private readonly ClassTypeRepository $classTypeRepository;
    private readonly GroupStudentRepository $groupStudentRepository;


    public function __construct(
        EntityManagerInterface $entityManager,
        TeacherRepository $teacherRepository,
        RoomRepository $roomRepository,
        ClassGroupRepository $classGroupRepository,
        SubjectRepository $subjectRepository,
        DepartmentRepository $departmentRepository,
        StudentRepository $studentRepository,
        ClassPeriodRepository $classPeriodRepository,
        ClassTypeRepository $classTypeRepository,
        GroupStudentRepository $groupStudentRepository
    ) {
        $this->entityManager = $entityManager;
        $this->teacherRepository = $teacherRepository;
        $this->roomRepository = $roomRepository;
        $this->classGroupRepository = $classGroupRepository;
        $this->subjectRepository = $subjectRepository;
        $this->departmentRepository = $departmentRepository;
        $this->studentRepository = $studentRepository;
        $this->classPeriodRepository = $classPeriodRepository;
        $this->classTypeRepository = $classTypeRepository;
        $this->groupStudentRepository = $groupStudentRepository;
    }


    public function teacherDownload(): Response
    {
        ini_set("memory_limit", "-1");
        set_time_limit(3600);

        $this->entityManager->createQuery('DELETE FROM App\Entity\Teacher')->execute();

        $url = 'https://plan.zut.edu.pl/schedule.php?kind=teacher&query=';
        $json = file_get_contents($url);

        $data = json_decode($json, true);

        if ($data === null) {
            throw new \Exception("Błąd podczas dekodowania JSON.");
        }

        foreach ($data as $entry) {
            $teacherName = $entry['item'];

            if (!isset($teacherName)) {
                continue;
            }

            if (!$this->teacherRepository->findOneBy(['fullName' => $teacherName])) {
                $teacher = new Teacher();
                $teacher->setFullName($entry['item']);
                $this->entityManager->persist($teacher);
            }
        }

        $this->entityManager->flush();

        return new Response('Downloaded Teachers');
    }

    public function roomDownload(): Response
    {
        ini_set("memory_limit", "-1");
        set_time_limit(3600);

        $this->entityManager->createQuery('DELETE FROM App\Entity\Room')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\Department')->execute();

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
                    $department = $this->departmentRepository->findOneBy(['name' => $departmentName]);

                    if (!$department) {
                        $department = new Department();
                        $department->setName($departmentName);
                        $this->entityManager->persist($department);
                    }

                    $departments[$departmentName] = $department;

                } else {
                    $department = $departments[$departmentName];
                }

                if(!$this->roomRepository->findOneBy(
                    ['number' => $roomNumber, 'department' => $department])) {

                    $room = new Room();
                    $room->setNumber($roomNumber);
                    $room->setDepartment($department);
                    $this->entityManager->persist($room);
                }
            }
        }

        $this->entityManager->flush();

        return new Response('Downloaded Rooms and Departments');
    }

    public function subjectDownload(): Response
    {
        ini_set("memory_limit", "-1");
        set_time_limit(3600);

        $this->entityManager->createQuery('DELETE FROM App\Entity\Subject')->execute();

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

                if (!$this->subjectRepository->findOneBy(['name' => $sub])) {
                    $subject = new Subject();
                    $subject->setName($sub);

                    $this->entityManager->persist($subject);
                }
            }
        }

        $this->entityManager->flush();

        return new Response('Downloaded Subjects');
    }

    public function groupDownload(): Response
    {
        ini_set("memory_limit", "-1");
        set_time_limit(3600);

        $this->entityManager->createQuery('DELETE FROM App\Entity\ClassGroup')->execute();
        
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

                if (!$this->classGroupRepository->findOneBy(['number' => $groupName])) {
                    $group = new ClassGroup();
                    $group->setNumber($groupName);

                    $this->entityManager->persist($group);
                }
            }
        }

        $this->entityManager->flush();

        return new Response('Downloaded Groups (not all)');
    }

    public function classPeriodDownload($start, $end): Response
    {
        ini_set("memory_limit", "-1");
        set_time_limit(3600);
        date_default_timezone_set('Europe/Warsaw');

        $this->entityManager->createQueryBuilder()
            ->delete(ClassPeriod::class, 'cp')
            ->where('cp.datetimeStart >= :start')
            ->andWhere('cp.datetimeStart <= :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->getQuery()
            ->execute();

        $classTypes = [];
        $classPeriods = [];
        $newGroups = [];

        for ($i = 51050; $i < 51150; $i++) {
            $url = 'https://plan.zut.edu.pl/schedule_student.php?number=' . $i . '&start=' . $start . '&end=' . $end;
            $json = file_get_contents($url);

            if (!str_contains($json, ',')) {
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

            $student = $this->studentRepository->findOneBy(['studentIndex' => $i]);
            if (!$student) {
                $student = new Student();
                $student->setStudentIndex($i);
                $this->entityManager->persist($student);
            }

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

                    if (!$this->groupStudentRepository->findOneBy(['group' => $group, 'student' => $student])) {
                        $groupStudent = new GroupStudent();
                        $groupStudent->setStudent($student);
                        $groupStudent->setGroup($group);
                        $this->entityManager->persist($groupStudent);
                    }
                }

                if (!$group) {
                    $group = $this->classGroupRepository->findOneBy(['number' => $groupNumber]);
                    if (!$group) {
                        $group = $newGroups[$groupNumber];
                    }
                }

                $classTypeName = $entry['lesson_form'];

                if (!array_key_exists($classTypeName, $classTypes)) {
                    $classType = $this->classTypeRepository->findOneBy(['type' => $classTypeName]);

                    if (!$classType) {
                        $classType = new ClassType();
                        $classType->setType($classTypeName);
                        $classTypes[$classTypeName] = $classType;
                        $this->entityManager->persist($classType);
                    }
                }
                else {
                    $classType = $classTypes[$classTypeName];
                }

                $teacherName = $entry['worker'];
                $teacher = $this->teacherRepository->findOneBy(['fullName' => $teacherName]);

                if (!$teacher) {
                    echo "Worker: " . $teacherName . PHP_EOL;
                    $teacher = new Teacher();
                    $teacher->setFullName($teacherName);
                    $this->entityManager->persist($teacher);
                    $this->entityManager->flush();
                }

                if (str_contains($entry['room'], ' ')) {
                    $roomName = explode(' ', $entry['room'], 2)[1];
                }
                else {
                    $roomName = explode('_', $entry['room'], 2)[1];
                }

                $room = $this->roomRepository->findOneBy(['number' => $roomName]);

                $subjectName = mb_convert_case($entry['subject'], MB_CASE_TITLE, "UTF-8");
                $subject = $this->subjectRepository->findOneBy(['name' => $subjectName]);

                if (!$subject) {
                    echo "Subject: " . $subjectName . PHP_EOL;
                    $subject = new Subject();
                    $subject->setName($subjectName);
                    $this->entityManager->persist($subject);
                    $this->entityManager->flush();
                }

                $dateStart = new \DateTime($entry['start']);
                $dateEnd = new \DateTime($entry['end']);

                $classPeriod = $this->classPeriodRepository->findOneBy(([
                    'group' => $group,
                    'room' => $room,
                    'subject' => $subject,
                    'teacher' => $teacher,
                    'classType' => $classType,
                    'datetimeStart' => $dateStart,
                    'datetimeStop' => $dateEnd
                ]));

                if (!$classPeriod) {
                    $classPeriod = new ClassPeriod();
                    $classPeriod->setgroup($group);
                    $classPeriod->setClassType($classType);
                    $classPeriod->setRoom($room);
                    $classPeriod->setTeacher($teacher);
                    $classPeriod->setSubject($subject);
                    $classPeriod->setDatetimeStart($dateStart);
                    $classPeriod->setDatetimeStop($dateEnd);

                    $classKey = $teacherName .
                        $dateStart->format('Y m d H i s') .
                        $dateEnd->format('Y m d H i s');

                    if (!array_key_exists($classKey, $classPeriods)) {
                        $classPeriods[$classKey] = $classPeriod;
                        $this->entityManager->persist($classPeriod);
                    }
                }
            }

            $this->entityManager->flush();
        }

        return new Response('Downloaded Students, Class Types, Class Periods and remaining groups');
    }

    public function classPeriodDownloadWrapper(bool $wholeSemester) {
        if ($wholeSemester) {
            $semesterData = Yaml::parseFile(__DIR__.'/../../config/semester.yaml');

            $start = (new \DateTime($semesterData['previous-semester']['start']))->format('Y-m-d');
            $end = (new \DateTime($semesterData['current-semester']['end']))->format('Y-m-d');

            $this->entityManager->createQuery('DELETE FROM App\Entity\ClassPeriod')->execute();
            $this->entityManager->createQuery('DELETE FROM App\Entity\ClassType')->execute();
            $this->entityManager->createQuery('DELETE FROM App\Entity\Room')->execute();
            $this->entityManager->createQuery('DELETE FROM App\Entity\Department')->execute();
            $this->entityManager->createQuery('DELETE FROM App\Entity\GroupStudent')->execute();
            $this->entityManager->createQuery('DELETE FROM App\Entity\Student')->execute();
            $this->entityManager->createQuery('DELETE FROM App\Entity\ClassGroup')->execute();
            $this->entityManager->createQuery('DELETE FROM App\Entity\Subject')->execute();
            $this->entityManager->createQuery('DELETE FROM App\Entity\Teacher')->execute();

            $this->teacherDownload();
            $this->roomDownload();
            $this->subjectDownload();
            $this->groupDownload();
        }
        else {
            $start = date('Y-m-d');
            $end = date('Y-m-d', strtotime('+7 days'));
        }

        $this->classPeriodDownload($start, $end);
    }

    //deprecated
    public function apiDataDownload(): Response
    {
        ini_set("memory_limit", "-1");
        set_time_limit(3600);

        try{
            echo $this->teacherDownload() . PHP_EOL;
            echo $this->roomDownload() . PHP_EOL;
            echo $this->subjectDownload() . PHP_EOL;
            echo $this->groupDownload() . PHP_EOL;
            echo $this->classPeriodDownload() . PHP_EOL;
            return new Response('Data downloaded');
        }
        catch (\Exception $e)
        {
            echo $e->getMessage() . PHP_EOL;
            return new Response('Error: ' . $e->getMessage());
        }
    }
}