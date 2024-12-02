<?php

namespace App\Controller;

use App\Repository\SubjectRepository;
use App\Repository\TeacherRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;


class ScheduleController extends AbstractController
{
    private TeacherRepository $teacherRepository;
    private SubjectRepository $subjectRepository;
    public function __construct(TeacherRepository $teacherRepository,
    SubjectRepository $subjectRepository)
    {
        $this->teacherRepository = $teacherRepository;
        $this->subjectRepository = $subjectRepository;
    }
    public function indexAction(): Response
    {
        //$this->teacherRepository->fetchAndSaveTeachers();
        //echo $this->teacherRepository->findByName("Karczmarczyk Artur") ? 'true' : 'false';
        //echo $this->subjectRepository->saveToDb("Aplikacje Internetowe 1 (WI, informatyka, SS, SPS)") ? 'true' : 'false';

        return $this->render('schedule.html.twig', []);
    }
}