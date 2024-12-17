<?php

namespace App\Controller;

use App\Repository\ClassGroupRepository;
use App\Repository\ClassTypeRepository;
use App\Repository\DepartmentRepository;
use App\Repository\RoomRepository;
use App\Repository\SubjectRepository;
use App\Repository\TeacherRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends AbstractController
{
    #[Route('/api/teacher', name: 'api_teacher', methods: ['GET'])]
    public function getTeacher(Request $request, TeacherRepository $repository): JsonResponse
    {
        $filter = $request->query->get('filter');
        $searchParts = array_filter(explode(' ', strtolower($filter)));

        $qb = $repository->createQueryBuilder('t');

        foreach ($searchParts as $index => $part) {
            $qb->andWhere($qb->expr()->like('LOWER(t.fullName)', ':part' . $index))
                ->setParameter('part' . $index, '%' . $part . '%');
        }

        $teachers = $qb->setMaxResults(5)
            ->getQuery()
            ->getResult();

        $teacherNames = array_map(fn($teacher) => $teacher->getFullName(), $teachers);

        return new JsonResponse($teacherNames);
    }

    #[Route('/api/subject', name: 'api_subject', methods: ['GET'])]
    public function getSubject(Request $request, SubjectRepository $repository): JsonResponse
    {
        $filter = $request->query->get('filter');

        $qb = $repository->createQueryBuilder('s');
        $objects = $qb->where('s.name LIKE :filter')
            ->setParameter('filter', '%' . $filter . '%')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

        $objectNames = array_map(fn($object) => $object->getName(), $objects);

        return new JsonResponse($objectNames);
    }

    #[Route('/api/department', name: 'api_department', methods: ['GET'])]
    public function getDepartment(Request $request, DepartmentRepository $repository): JsonResponse
    {
        $filter = $request->query->get('filter');

        $qb = $repository->createQueryBuilder('d');
        $objects = $qb->where('d.name LIKE :filter')
            ->setParameter('filter', '%' . $filter . '%')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

        $objectNames = array_map(fn($object) => $object->getName(), $objects);

        return new JsonResponse($objectNames);
    }

    #[Route('/api/room', name: 'api_room', methods: ['GET'])]
    public function getRoom(Request $request, RoomRepository $repository): JsonResponse
    {
        $filter = $request->query->get('filter');

        $qb = $repository->createQueryBuilder('r');
        $objects = $qb->where('r.number LIKE :filter')
            ->setParameter('filter', '%' . $filter . '%')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

        $objectNames = array_map(fn($object) => $object->getNumber(), $objects);

        return new JsonResponse($objectNames);
    }

    #[Route('/api/group', name: 'api_group', methods: ['GET'])]
    public function getGroup(Request $request, ClassGroupRepository $repository): JsonResponse
    {
        $filter = $request->query->get('filter');

        $qb = $repository->createQueryBuilder('cg');
        $objects = $qb->where('cg.number LIKE :filter')
            ->setParameter('filter', '%' . $filter . '%')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

        $objectNames = array_map(fn($object) => $object->getNumber(), $objects);

        return new JsonResponse($objectNames);
    }

    #[Route('/api/class-type', name: 'api_class_type', methods: ['GET'])]
    public function getClassType(Request $request, ClassTypeRepository $repository): JsonResponse
    {
        $filter = $request->query->get('filter');

        $qb = $repository->createQueryBuilder('ct');
        $objects = $qb->where('ct.type LIKE :filter')
            ->setParameter('filter', '%' . $filter . '%')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

        $objectNames = array_map(fn($object) => $object->getType(), $objects);

        return new JsonResponse($objectNames);
    }
}