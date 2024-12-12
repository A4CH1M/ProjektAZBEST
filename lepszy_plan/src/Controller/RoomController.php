<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\RoomRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class RoomController extends AbstractController
{
    #[Route('/api/room', name: 'api_room', methods: ['GET'])]
    public function getRoom(Request $request, RoomRepository $repository): JsonResponse
    {
        $filter = $request->query->get('filter');

        $qb = $repository->createQueryBuilder('r');
        $objects = $qb->where('r.number LIKE :filter')
            ->setParameter('filter', $filter . '%')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

        $objectNames = array_map(fn($object) => $object->getNumber(), $objects);

        return new JsonResponse($objectNames);
    }
}