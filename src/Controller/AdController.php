<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Image;
use App\Form\AjouterAnnonceType;
use App\Repository\AdRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
class AdController extends AbstractController
{
    /**
     * @Route("/ads", name="ads_index")
     */
    public function index(AdRepository $repo)
    {
        //si on ajoute AdRepository pas besoin de cette ligne
       // $repo = $this->getDoctrine()->getRepository(Ad::class);
        $ads = $repo->findAll();
        return $this->render('ad/index.html.twig', [
            'ads' => $ads
        ]);
    }

    /**
     * Permet de créer une annonce
     * @Route("/ads/new",name="ads_create")
     * 
     * sécurité afin de vérifier que l'utilisateur est bien connecté a un compte pour créer un post
     * @IsGranted("ROLE_USER")
     * @return Response
     */
    public function create(Request $request,ObjectManager $manager){
        $ad = new Ad();
        

        $form = $this->createForm(AjouterAnnonceType::class,$ad);

        $form->handleRequest($request);

        //verif du formulaire
        if($form->isSubmitted() && $form->isValid()){
          foreach($ad->getImages() as $image){
              $image->setAd($ad);
              $manager->persist($image);
          }
          $ad->setAuthor($this->getUser());
          //Pas besoin de cette ligne si on appel ObjectManager
            // $manager = $this->getDoctrine()->getManager();
           $manager->persist($ad);
           $manager->flush();

           //message success annonce ajoutée
           $this->addFlash(
               'success',
                "l'annonce<strong>{$ad->getTitle()}</strong> a bien été enregistrée"
           );

           
            //envoi direct sur l'article crée
           return $this->redirectToRoute('ads_show',[
               'slug' => $ad->getSlug()
           ]);
        }

        return $this->render('ad/new.html.twig',[
            'form' =>$form->createView()
        ]);
       }

/**
 * Permet d'afficher une seule annonce
 * @Route("/ads/{slug}",name="ads_show")
 * @return Response
 */
    public function show($slug,Ad $ad,AdRepository $repo){
        //Je récupère l'annonce qui correspond au slug
        // $ad = $repo->findOneBySlug($slug);

     return $this->render('ad/show.html.twig',[
         'ad' => $ad
     ]);
    }
    
    /**
     * Permet d'afficher le formulaire d'édition
     * @Route("/ads/{slug}/edit", name="ads_edit")
     * 
     * sécurité pour éviter qu'un utilisateur modifie une annonce d'un autre
     * @Security("is_granted('ROLE_USER') and user === ad.getAuthor()",message="Cette annonce ne vous appartient pas,vous ne pouvez pas la modifier")
     * @return Response
     */
    public function edit(Ad $ad,Request $request,ObjectManager $manager){
     
        $form = $this->createForm(AjouterAnnonceType::class,$ad);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            foreach($ad->getImages() as $image){
                $image->setAd($ad);
                $manager->persist($image);
            }
            
            //Pas besoin de cette ligne si on appel ObjectManager
              // $manager = $this->getDoctrine()->getManager();
             $manager->persist($ad);
             $manager->flush();
  
             //message success annonce ajoutée
             $this->addFlash(
                 'success',
                  "Les modifications de l'annonce<strong>{$ad->getTitle()}</strong> a bien été modifiée"
             );
  
            return $this->redirectToRoute('ads_show',[
                'slug' =>$ad->getSlug()
            ]);


            }
            return $this->render('ad/edit.html.twig',[
                'form' => $form->createView(),
                'ad' => $ad
            ]);
       }
       /**
        * Permet de supprimer une annonce
        * @Route("/ads/{slug}/delete" , name="ads_delete")
        * Security("is_granted('ROLE_USER') and user == ad.getAuthor()", message= " Vous n'avez pas le droit d'accéder à cette ressource")
        */
       public function delete(Ad $ad,ObjectManager $manager){
               $manager->remove($ad);
               $manager->flush();

               $this->addFlash(
                   'success',
                   "L'annonce {$ad->getTitle()} a bien été supprimée"
               );

               return $this->redirectToRoute("ads_index");
       }
    }
