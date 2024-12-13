<?php

namespace App\Controller\API;

use App\Repository\ClassGroupRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ClassGroupController extends AbstractController
{
    #[Route('/api/class-group', name: 'api_class_group', methods: ['GET'])]
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