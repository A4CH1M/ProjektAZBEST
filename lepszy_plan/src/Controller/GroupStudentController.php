<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use App\Repository\GroupStudentRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class GroupStudentController extends AbstractController
{
    #[Route('/api/groupStudent', name: 'api_groupStudent', methods: ['GET'])]
    public function getStudentGroup(Request $request, GroupStudentRepository $groupStudentRepository): JsonResponse
    {
        // Pobierz wartość filtru z parametrów zapytania
        $filterStudent = $request->query->get('filter_student');

        if ($filterStudent) {
            // Znajdź nauczycieli pasujących do filtra
            $groups = $groupStudentRepository->findGroupsByStudentId($filterStudent);
        }

        // Przekształć wynik na tablicę grup
        //$groupsNames = array_map(fn($group) => $group->getGroup()->getNumber(), $groups);
        $groupsNames = array_map(fn($group) => $group['number'], $groups);

        return new JsonResponse($groupsNames);
    }

//    #[Route('/api/teachers', name: 'get_teachers', methods: ['GET'])]
//    public function getTeachers(TeacherRepository $teacherRepository): JsonResponse
//    {
//        // Pobieranie wszystkich rekordów z kolumny `full_name`
//        $teachers = $teacherRepository->findAllFullNames();
//
//        return $this->json($teachers);
//    }
}