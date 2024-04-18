<?php

namespace App\Controller;

use App\Repository\AnnoncesRepository;
use App\Repository\ReservationRepository;
use App\Repository\UserRepository;
use App\Service\FakerSlider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private $userRepository;
    private $annoncesRepository;
    private $reservationRepository;

    public function __construct(UserRepository $userRepository, ReservationRepository $reservationRepository, AnnoncesRepository $annoncesRepository)
    {
        $this->userRepository = $userRepository;
        $this->annoncesRepository = $annoncesRepository;
        $this->reservationRepository = $reservationRepository;
    }

    #[Route('/', name: 'app_home')]
    public function index(FakerSlider $fakerSlider): Response
    {
        $images = $fakerSlider->generateFakeData();
        $topUsers = $this->userRepository->findTopThreeUsersWithBestRatings();
        $topAnnonces = $this->annoncesRepository->findTopThreeAnnoncesWithBestRatings();
        $reservations
            = $this->reservationRepository->findAll();
        $annonces = $this->annoncesRepository->findAll();
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'topUsers' => $topUsers,
            'topAnnonces' => $topAnnonces,
            'reservations' => $reservations,
            'annonces' => $annonces,
            'images' => $images
        ]);
    }
}
