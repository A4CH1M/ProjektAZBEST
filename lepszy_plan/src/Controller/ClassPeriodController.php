<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\ClassPeriodRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class ClassPeriodController extends AbstractController
{
    #[Route('/api/classPeriod', name: 'api_classPeriod', methods: ['GET'])]
    public function getClassPeriods(Request $request, ClassPeriodRepository $classPeriodRepository): JsonResponse
    {
        $student = $request->query->get('student'); // dodatkowa operacja do wyciągnięcia grup studentów
        $group = $request->query->get('group');
        $room = $request->query->get('room');
        $subject = $request->query->get('subject');
        $teacher = $request->query->get('teacher');
        $classType = $request->query->get('class_type');

        $classPeriods = [];
        if ($student || $group || $room || $subject || $teacher || $classType) {
            $qb = $classPeriodRepository->createQueryBuilder('cp'); // 'cp' to alias dla ClassPeriod

            if ($student) {
                $qb->join('cp.subject', 'sub')
                    ->join('App\Entity\GroupStudent', 'gs', 'WITH', 'gs.group = cp.group')
                    ->join('App\Entity\Student', 's', 'WITH', 's.id = gs.student')
                    ->where('s.studentIndex = :studentIndex') // Filtrujemy po ID studenta
                    ->setParameter('studentIndex', $student);
            }

            // Dodanie warunków dynamicznie
            if ($teacher) {
                $qb->join('cp.teacher', 't')
                    ->andWhere('t.fullName = :teacher')
                    ->setParameter('teacher', $teacher);
            }

            //do sprawdzenia w konsoli jak ukladane jest zapytanie
            //return new JsonResponse(['dql' => $qb->getDQL()]);

            if ($group) {
                $qb->join('cp.group', 'g')
                    ->andWhere('g.number = :group')
                    ->setParameter('group', $group);
            }
            if ($room) {
                $qb->join('cp.room', 'r')
                    ->andWhere('r.number = :room')
                    ->setParameter('room', $room);
            }
            if ($subject) {
                $qb->join('cp.subject', 'subj')
                    ->andWhere('subj.name = :subject')
                    ->setParameter('subject', $subject);
            }
            if ($classType) {
                $qb->join('cp.classType', 'ct')
                    ->andWhere('ct.type = :classType')
                    ->setParameter('classType', $classType);
            }

            // Pobranie wyników
            $classPeriods = $qb->getQuery()->getResult();
        }

        $details = array_map(fn($classPeriod) => [
            'subject' => $classPeriod->getSubject()->getName(),
            'start' => $classPeriod->getDatetimeStart()->format('Y-m-d H:i:s'),
            'end' => $classPeriod->getDatetimeStop()->format('Y-m-d H:i:s'),
            'teacher' => $classPeriod->getTeacher()->getFullName(),
            'group' => $classPeriod->getGroup()->getNumber(),
            'room' => $classPeriod->getRoom()->getNumber(),
            'department' => $classPeriod->getRoom()->getDepartment()->getName(),
            'class_type' => $classPeriod->getClassType()->getType(),
        ], $classPeriods);

        return new JsonResponse($details);
    }
}