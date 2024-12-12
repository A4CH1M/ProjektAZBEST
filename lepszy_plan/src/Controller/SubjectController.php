<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\SubjectRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class SubjectController extends AbstractController
{
    #[Route('/api/subject', name: 'api_subject', methods: ['GET'])]
    public function getSubject(Request $request, SubjectRepository $repository): JsonResponse
    {
        $filter = $request->query->get('filter');

        $qb = $repository->createQueryBuilder('s');
        $subjects = $qb->where('s.name LIKE :filter')
            ->setParameter('filter', $filter . '%')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

        $subjectNames = array_map(fn($subject) => $subject->getName(), $subjects);

        return new JsonResponse($subjectNames);
    }
}