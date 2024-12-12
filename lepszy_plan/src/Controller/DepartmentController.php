<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\DepartmentRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

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