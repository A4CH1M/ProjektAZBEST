<?php

namespace App\Controller\API;

use App\Repository\ClassTypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ClassTypeController extends AbstractController
{
    #[Route('/api/class-type', name: 'api_class_type', methods: ['GET'])]
    public function getClassType(Request $request, ClassTypeRepository $repository): JsonResponse
    {
        $filter = $request->query->get('filter');

        $qb = $repository->createQueryBuilder('ct');
        $objects = $qb->where('ct.type LIKE :filter')
            ->setParameter('filter', $filter . '%')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

        $objectNames = array_map(fn($object) => $object->getType(), $objects);

        return new JsonResponse($objectNames);
    }
}