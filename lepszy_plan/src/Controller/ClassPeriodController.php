<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\ClassPeriodRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class ClassPeriodController extends AbstractController
{
    #[Route('/api/class-period', name: 'api_classPeriod', methods: ['GET'])]
    public function getClassPeriods(Request $request, ClassPeriodRepository $classPeriodRepository): JsonResponse
    {
        $student = $request->query->get('student');
        $group = $request->query->get('group');
        $room = $request->query->get('room');
        $subject = $request->query->get('subject');
        $teacher = $request->query->get('teacher');
        $classType = $request->query->get('class_type');
        $filterLogic = strtoupper($request->query->get('filter_logic', 'AND'));

        $classPeriods = [];
        if ($student || $group || $room || $subject || $teacher || $classType) {
            $qb = $classPeriodRepository->createQueryBuilder('cp');

            $joinType = $filterLogic === 'OR' ? 'leftJoin' : 'innerJoin';

            if ($student) {
                $qb->$joinType('cp.subject', 'sub')
                    ->$joinType('App\Entity\GroupStudent', 'gs', 'WITH', 'gs.group = cp.group')
                    ->$joinType('App\Entity\Student', 's', 'WITH', 's.id = gs.student')
                    ->where('s.studentIndex = :studentIndex')
                    ->setParameter('studentIndex', $student);
            }

            if ($teacher) {
                $qb->$joinType('cp.teacher', 't')
                    ->andWhere('t.fullName = :teacher')
                    ->setParameter('teacher', $teacher);
            }

            if ($group) {
                $qb->$joinType('cp.group', 'g')
                    ->andWhere('g.number = :group')
                    ->setParameter('group', $group);
            }

            if ($room) {
                $qb->$joinType('cp.room', 'r')
                    ->andWhere('r.number = :room')
                    ->setParameter('room', $room);
            }

            if ($subject) {
                $qb->$joinType('cp.subject', 'subj')
                    ->andWhere('subj.name = :subject')
                    ->setParameter('subject', $subject);
            }

            if ($classType) {
                $qb->$joinType('cp.classType', 'ct')
                    ->andWhere('ct.type = :classType')
                    ->setParameter('classType', $classType);
            }

            if ($filterLogic === 'OR') {
                $qb->orWhere(
                    $qb->expr()->orX(
                        $student ? 's.studentIndex = :studentIndex' : '1=0',
                        $teacher ? 't.fullName = :teacher' : '1=0',
                        $group ? 'g.number = :group' : '1=0',
                        $room ? 'r.number = :room' : '1=0',
                        $subject ? 'subj.name = :subject' : '1=0',
                        $classType ? 'ct.type = :classType' : '1=0',
                    )
                );
            }

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