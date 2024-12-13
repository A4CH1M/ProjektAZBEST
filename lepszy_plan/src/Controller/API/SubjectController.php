<?php

namespace App\Controller\API;

use App\Repository\SubjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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