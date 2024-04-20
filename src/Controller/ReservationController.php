<?php

namespace App\Controller;

use App\Entity\Annonces;
use App\Entity\Commentaire;
use App\Entity\Reservation;
use App\Form\CommentaireType;
use App\Form\ReservationType;
use App\Repository\AnnoncesRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReservationController extends AbstractController
{
    #[Route('/annonces/{slug}/reservation', name: 'app_reservation')]
    public function index($slug, AnnoncesRepository $repo, Request $request, EntityManagerInterface $entityManager): Response
    {


        $reservation = new Reservation();
        $annonces
            = $repo->findOneBy(['slug' => $slug]);
        $form = $this->createForm(ReservationType::class, $reservation);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            var_dump($reservation);
            $user = $this->getUser();
            $reservation->setPersonneResa($user)
                ->setAnnonce($annonces);
            $entityManager->persist($reservation);
            $entityManager->flush();
            //Rediriger vers la page et passe en paramètre l'id de la réservation
            return $this->redirectToRoute('reservation_show', ['id' => $reservation->getId(), 'withAlert' => true]);
        }

        return $this->render('reservation/reservation.html.twig', [
            'annonces' => $annonces,
            'form' => $form->createView()
        ]);
    }
    #[Route('/reservation/{id}', name: 'reservation_show')]
    public function show(ReservationRepository $repo, Request $request, EntityManagerInterface $entityManager, $id)
    {
        $commentaire = new Commentaire();
        $form = $this->createForm(CommentaireType::class, $commentaire);
        $reservation
            = $repo->findOneBy(['id' => $id]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $commentaire->setAnnonce($reservation->getAnnonce())
                ->setAuteur($this->getUser());
            $entityManager->persist($commentaire);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Votre commentaire a bien été pris en compte !'
            );
        }

        return $this->render('reservation/show.html.twig', [
            'reservation' => $reservation,
            'form' => $form->createView(),
        ]);
    }
}
