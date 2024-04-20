<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AjouterAnnonceType;
use App\Repository\AdRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminAdController extends AbstractController
{
    /**
     * @Route("/admin/ads/{page<\d+>?1}", name="admin_ads_index", requirements={"page": "\d+"})
     */
    public function index(AdRepository $repo,$page = 1)
    {
        //documentation de doctrine
        //methode find: Permet de retrouver un enregistrement par son identifiant
        // $ad = $repo->find(123);
        //methode pour rechercher par titre
        //$ad = $repo->findOneBy([
        //   'titre' => "test"
        //]);
           //5  pagination à partir de zero
        //$ads = $repo->findBy([],[],5,0);
        //dump($ad);

        $limit = 10;

        $start = $page * $limit - $limit;
        //page1 * 10 = 10 - 10 = 0
        //page2 * 10 = 20 - 10 = 10
        
        //Pour trouver le nombre de page
        $total = count($repo->findAll());
        //fonction ceil de php qui arrondi au dessus
        $pages = ceil($total / $limit);

        return $this->render('admin/ad/index.html.twig', [
            //1 tableau de critère de recherche
            //2 tableau de critère pour ordonner les informations
            //3 le nombre d'annonce par page
            //4 a partir de ou
            'ads' => $repo->findBy([],[],$limit,$start),
            'pages' => $pages,
            'page' => $page
        ]);
    }

    /**
     * Permet de modifier une annonce
     *@Route("/admin/ads/{id}/edit", name="admin_ads_edit")
     */

     public function edit(Ad $ad,Request $request,ObjectManager $manager){

         $form = $this->createForm(AjouterAnnonceType::class,$ad);

         $form->handleRequest($request);

         if($form->isSubmitted() && $form->isValid()){
             $manager->persist($ad);
             $manager->flush();

             $this->addFlash(
                 'success',
                 "L'annonce <strong>{$ad->getTitle()}</strong> a bien été enregistrée !"
             );
         }
         return $this->render('admin/ad/edit.html.twig',[
             'ad' => $ad,
             'form' => $form->createView()
         ]);
     }

     /**
      * Permet de supprimer une annonce
      *@Route("/admin/ads/{id}/delete", name="admin_ads_delete")
      */
     public function delete(Ad $ad, ObjectManager $manager){
        /* si l'annonce a des réservations impossible de supprimer */
        if(count($ad->getBookings()) > 0){
            $this->addFlash(
                'warning',
                "Vous ne pouvez pas supprimer l'annonce <strong>{$ad->getTitle()}</strong> car elle possède déjà des réservations"
            );

        }else{

        $manager->remove($ad);
        $manager->flush();

        $this->addFlash(
            'success',
            "L'annonce <strong>{$ad->getTitle()}/strong> a bien été supprimée"
        );
        return $this->redirectToRoute('admin_ads_index');

     }
  }
}
