<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ScheduleController extends AbstractController
{
    public function indexAction(): Response
    {
        return $this->render('schedule.html.twig', []);
    }
}