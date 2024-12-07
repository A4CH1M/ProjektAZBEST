<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use App\Repository\TeacherRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class TeacherController extends AbstractController
{
    #[Route('/api/teachers', name: 'api_teachers', methods: ['GET'])]
    public function getTeachers(Request $request, TeacherRepository $teacherRepository): JsonResponse
    {
        // Pobierz wartość filtru z parametrów zapytania
        $filterTeacher = $request->query->get('filter_teacher');

        if ($filterTeacher) {
            // Znajdź nauczycieli pasujących do filtra
            $teachers = $teacherRepository->findByName($filterTeacher);
        }
        //else {
        // Jeśli brak filtra, zwróć wszystkich nauczycieli (opcjonalne)
        //$teachers = $teacherRepository->findAll();
        //}

        // Przekształć wynik na tablicę nazwisk
        $teacherNames = array_map(fn($teacher) => $teacher->getFullName(), $teachers);

        return new JsonResponse($teacherNames);
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