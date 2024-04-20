<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends AbstractController
{
    #[Route('/account', name: 'app_account')]
    public function index(): Response
    {

        return $this->render('user/index.html.twig', [
            'user' => $this->getUser(),
        ]);
    }


    #[Route('/account/reservation', name: 'app_reservations')]
    public function reservation(): Response
    {

        return $this->render('user/mesreservations.html.twig', [
            'user' => $this->getUser(),
        ]);
    }
}
