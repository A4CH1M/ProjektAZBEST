<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\ClassPeriodRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class ClassPeroidController extends AbstractController
{
    #[Route('/api/classPeroid', name: 'api_classPeroid', methods: ['GET'])]
    public function getSubjects(Request $request, ClassPeriodRepository $classPeroidRepository): JsonResponse
    {
        // Pobierz wartość filtru z parametrów zapytania
        $filter = $request->query->get('filter_student');

        if ($filter) {
            // Znajdź nauczycieli pasujących do filtra
            $subjects = $classPeroidRepository->findSubjectsByIndex($filter);
        }

//        // Przekształć wynik na tablicę nazwisk
//        $names = array_map(fn($classPeroid) => $classPeroid->getSubject()->getName(), $subjects);
//
//        return new JsonResponse($names);

        $details = array_map(fn($classPeriod) => [
            'name' => $classPeriod->getSubject()->getName(),
            'start' => $classPeriod->getDatetimeStart()->format('Y-m-d H:i:s'), // Formatowanie daty na czytelny format
            'end' => $classPeriod->getDatetimeStop()->format('Y-m-d H:i:s'),
            'teacher' => $classPeriod->getTeacher()->getFullName(),
            'group' => $classPeriod->getGroup()->getNumber(),
            'room' => $classPeriod->getRoom()->getNumber(),
            'department' => $classPeriod->getRoom()->getDepartment()->getName(),
            'class_type' => $classPeriod->getClassType()->getType(),
        ], $subjects);

        return new JsonResponse($details);
    }
}