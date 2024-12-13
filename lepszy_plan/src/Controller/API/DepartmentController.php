<?php

namespace App\Controller\API;

use App\Repository\DepartmentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DepartmentController extends AbstractController
{
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
}