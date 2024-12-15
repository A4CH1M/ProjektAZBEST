<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\ClassPeriodRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class ClassPeriodController extends AbstractController
{
    #[Route('/api/class-period', name: 'api_class_period', methods: ['GET'])]
    public function getClassPeriods(Request $request, ClassPeriodRepository $classPeriodRepository): JsonResponse
    {
        $student1 = $request->query->get('student1');
        $student2 = $request->query->get('student2');
        $teacher1 = $request->query->get('teacher1');
        $teacher2 = $request->query->get('teacher2');
        $group = $request->query->get('group');
        $room = $request->query->get('room');
        $subject = $request->query->get('subject');
        $classType = $request->query->get('class_type');
        $filterLogic = strtoupper($request->query->get('filter_logic', 'AND'));

        $teacherCount = ($teacher1 ? 1 : 0) + ($teacher2 ? 1 : 0);
        $studentCount = ($student1 ? 1 : 0) + ($student2 ? 1 : 0);

        if (!(
            ($teacherCount === 2 && $studentCount === 0) ||
            ($teacherCount === 0 && $studentCount === 2) ||
            ($teacherCount === 1 && $studentCount === 1) ||
            ($teacherCount <= 1 && $studentCount <= 1)
        )) {
            return new JsonResponse([
                'error' => 'Invalid combination'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $qb = $classPeriodRepository->createQueryBuilder('cp');
        $joinType = $filterLogic === 'OR' ? 'leftJoin' : 'innerJoin';

        $conditions = [];

        if ($teacher1 || $teacher2) {
            if ($teacher1) {
                $qb->$joinType('cp.teacher', 't1');
                $conditions[] = 't1.fullName = :teacher1';
                $qb->setParameter('teacher1', $teacher1);
            }
            if ($teacher2) {
                $qb->$joinType('cp.teacher', 't2');
                $conditions[] = 't2.fullName = :teacher2';
                $qb->setParameter('teacher2', $teacher2);
            }
        }

        if ($student1 || $student2) {
            if ($student1) {
                $qb->$joinType('App\Entity\GroupStudent', 'gs1', 'WITH', 'gs1.group = cp.group');
                $qb->$joinType('App\Entity\Student', 's1', 'WITH', 's1.id = gs1.student');
                $conditions[] = 's1.studentIndex = :student1';
                $qb->setParameter('student1', $student1);
            }
            if ($student2) {
                $qb->$joinType('App\Entity\GroupStudent', 'gs2', 'WITH', 'gs2.group = cp.group');
                $qb->$joinType('App\Entity\Student', 's2', 'WITH', 's2.id = gs2.student');
                $conditions[] = 's2.studentIndex = :student2';
                $qb->setParameter('student2', $student2);
            }
        }

        if ($group) {
            $qb->$joinType('cp.group', 'g');
            $conditions[] = 'g.number = :group';
            $qb->setParameter('group', $group);
        }

        if ($room) {
            $qb->$joinType('cp.room', 'r');
            $conditions[] = 'r.number = :room';
            $qb->setParameter('room', $room);
        }

        if ($subject) {
            $qb->$joinType('cp.subject', 'subj');
            $conditions[] = 'subj.name = :subject';
            $qb->setParameter('subject', $subject);
        }

        if ($classType) {
            $qb->$joinType('cp.classType', 'ct');
            $conditions[] = 'ct.type = :classType';
            $qb->setParameter('classType', $classType);
        }

        if (!empty($conditions)) {
            if ($filterLogic === 'OR') {
                $qb->andWhere($qb->expr()->orX(...$conditions));
            } else {
                $qb->andWhere($qb->expr()->andX(...$conditions));
            }
        }

        $classPeriods = $qb->getQuery()->getResult();

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