<?php

namespace App\Controller;


use App\Entity\Annonces;
use App\Form\AnnonceType;
use App\Repository\AnnoncesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class AnnoncesController extends AbstractController


{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    #[Route('/annonces', name: 'app_annonces')]
    public function index(AnnoncesRepository $repo): Response
    {
        $annonces = $repo->findAll();
        return $this->render('annonces/index.html.twig', [
            'annonces' => $annonces,
        ]);
    }


    #[Route('/annonces/new', name: 'app_annonce_ad')]
    public function create(Request $request, EntityManagerInterface $entityManager)
    {

        $annonces = new Annonces();
        $annonces->getImages();
        // $image = new Image();
        // $image->setUrl('httpfdkofdok');
        // $annonces->addImage($image);
        $form = $this->createForm(AnnonceType::class, $annonces);


        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($annonces->getImages() as $image) {
                $image->setAnnonce($annonces);
                $entityManager->persist($image);
            }
            $annonces->setAuteur($this->getUser());
            $entityManager->persist($annonces);
            $entityManager->flush();
            $this->addFlash('success', "{$annonces->getTitle()} a bien été enregistré");
            return $this->redirectToRoute('app_annonce', [
                'slug' => $annonces->getSlug()
            ]);
        }
        return $this->render('annonces/ad.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/annonces/{slug}/edit', name: 'app_annonce_edit')]
    public function edit(Request $request, EntityManagerInterface $entityManager, $slug)
    {
        $annonces = $this->entityManager->getRepository(Annonces::class)->findOneBy(['slug' => $slug]);
        // Créez le formulaire en utilisant le formulaire AnnonceType et l'entité Annonces
        $form = $this->createForm(AnnonceType::class, $annonces);

        // Traitez la soumission du formulaire
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($annonces->getImages() as $image) {
                $image->setAnnonce($annonces);
                $entityManager->persist($image);
            }
            $entityManager->persist($annonces);
            $entityManager->flush();
            $this->addFlash('success', "{$annonces->getTitle()} a bien été modifiée");
            return $this->redirectToRoute('app_annonce', [
                'slug' => $annonces->getSlug()
            ]);
        }
        // Rendre la vue avec le formulaire et l'entité Annonces
        return $this->render('annonces/edit.html.twig', [
            'form' => $form->createView(),
            'annonces' => $annonces,
        ]);
    }

    #[Route('/annonces/{slug}', name: 'app_annonce')]
    public function show($slug, AnnoncesRepository $repo): Response
    {

        $annonce
            = $repo->findOneBy(['slug' => $slug]);
        return $this->render('annonces/show.html.twig', [
            'annonce' => $annonce,

        ]);
    }



    #[Route('/annonces/{slug}/delete', name: 'app_annonce_delete')]



    public function delete(string $slug, AnnoncesRepository $repo, EntityManagerInterface $entityManager): Response
    {

        $annonce = $repo->findOneBy(['slug' => $slug]);

        $entityManager->remove($annonce);
        $entityManager->flush();
        $this->addFlash('success', 'L\'annonce a bien été supprimée');
        return $this->redirectToRoute("app_annonces");
    }
}
