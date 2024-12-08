<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\ClassPeriodRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class ClassPeriodController extends AbstractController
{
    #[Route('/api/class-period', name: 'api_classPeriod', methods: ['GET'])]
    public function getClassPeriods(Request $request, ClassPeriodRepository $classPeriodRepository): JsonResponse
    {
        $student = $request->query->get('student'); // dodatkowa operacja do wyciągnięcia grup studentów
        $group = $request->query->get('group');
        $room = $request->query->get('room');
        $subject = $request->query->get('subject');
        $teacher = $request->query->get('teacher');
        $classType = $request->query->get('class_type');

        if (/*przynajmniej jeden filtr został przekazany*/false) {
            // dowiedzieć się jak można użyć tego co napisałem niżej, bez konieczności wszystkich filtrów
            // nie rób błagam funkcji w repozytoriach, jeśli istnieją domyślne, które robią to samo
            $classPeriods = $classPeriodRepository->findAllBy(([
                                'group' => $group,
                                'room' => $room,
                                'subject' => $subject,
                                'teacher' => $teacher,
                                'classType' => $classType
                            ]));
        }

        $details = array_map(fn($classPeriod) => [
            'subject' => $classPeriod->getSubject()->getName(),
            'start' => $classPeriod->getDatetimeStart()->format('Y-m-d H:i:s'),
            'end' => $classPeriod->getDatetimeStop()->format('Y-m-d H:i:s'),
            'teacher' => $classPeriod->getTeacher()->getFullName(),
            'group' => $classPeriod->getGroup()->getNumber(),
            'room' => $classPeriod->getRoom()->getNumber(),
            'department' => $classPeriod->getRoom()->getDepartment()->getName(),
            'class_type' => $classPeriod->getClassType()->getType(),
        ], $classPeriods);

        return new JsonResponse($details);
    }
}