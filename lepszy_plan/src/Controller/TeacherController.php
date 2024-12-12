<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\TeacherRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class TeacherController extends AbstractController
{
    #[Route('/api/teacher', name: 'api_teacher', methods: ['GET'])]
    public function getTeacher(Request $request, TeacherRepository $repository): JsonResponse
    {
        $filter = $request->query->get('filter');

        //$teachers = $repository->findByNameLike($filter);
        $qb = $repository->createQueryBuilder('t');
        $teachers = $qb->where('t.fullName LIKE :teacher')
            ->setParameter('teacher', $filter . '%')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

        $teacherNames = array_map(fn($teacher) => $teacher->getFullName(), $teachers);

        return new JsonResponse($teacherNames);
    }
}