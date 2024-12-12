<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\ClassGroupRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class ClassGroupController extends AbstractController
{
    #[Route('/api/classGroup', name: 'api_classGroup', methods: ['GET'])]
    public function getClassGroup(Request $request, ClassGroupRepository $repository): JsonResponse
    {
        $filter = $request->query->get('filter');

        $qb = $repository->createQueryBuilder('cg');
        $objects = $qb->where('cg.number LIKE :filter')
            ->setParameter('filter', $filter . '%')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

        $objectNames = array_map(fn($object) => $object->getNumber(), $objects);

        return new JsonResponse($objectNames);
    }
}