<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Entity\Comment;
use App\Form\BookingType;
use App\Form\CommentType;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;

class BookingController extends AbstractController
{
    /**
     * @Route("/ads/{slug}/book", name="booking_create")
     * @IsGranted("ROLE_USER")
     */
    public function book(Ad $ad,Request $request,ObjectManager $manager)
    {
        $booking = new Booking();
        $form = $this->createForm(BookingType::class,$booking);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $user = $this->getUser();

            $booking->setBooker($user)
                    ->setAd($ad);

                    //Si les dates ne sont pas pisponible,message d'erreur
                    if(!$booking->isBookableDates()){
                        $this->addFlash(
                            'warning',
                            "Les dates que vous avez choisi ne peuvent être reservées: elles sont déjà prises."
                        );
                    }else{
                      //sinon enregistrement et redirection
                        $manager->persist($booking);
                        $manager->flush();
    
                        return $this->redirectToRoute('booking_show',['id' => $booking->getId(),
                        'withAlert' => true]);
                    }
                
        }

        return $this->render('booking/book.html.twig', [
           'ad' => $ad,
           'form' => $form->createVIew()
        ]);
    }

    /**
     * Permet d'afficher la page d'une réservation
     * @route("/booking/{id}", name="booking_show")
     * 
     */
    public function show(Booking $booking, Request $request, ObjectManager $manager){
        $comment = new Comment();

        $form = $this->createForm(CommentType::class,$comment);
      
        //vérification du form et enregistrement en bdd
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $comment->setAd($booking->getAd())
                   ->setAuthor($this->getUser());

                   $manager->persist($comment);
                   $manager->flush();
                   $this->addFlash(
                       'success',
                       "Votre commentaire a bien été pris en compte"
                   );
        } 

         return $this->render('booking/show.html.twig',[
             'booking' => $booking,
             'form' => $form->createView()
         ]);
    }
}
