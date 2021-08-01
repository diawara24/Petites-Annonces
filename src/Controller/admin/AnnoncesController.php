<?php

namespace App\Controller\admin;

use App\Entity\Annonces;
use App\Repository\AnnoncesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
/**
 * @Route("/admin/annonces", name="admin_annonces_")
 * Undocumented class
 */
class AnnoncesController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(AnnoncesRepository $repo): Response
    {
        $annonces = $repo->findAll();

        return $this->render('admin/annonces/index.html.twig', [
            'annonces' => $annonces
        ]);
    }

    /**
     * @Route("/activer/{id}", name="activer")
     */
    public function activer(Annonces $annonce, EntityManagerInterface $manager): Response
    {
        $annonce->setActive(($annonce->getActive())?false:true);
        $manager->persist($annonce);
        $manager->flush();
        return new Response("true");
    }

    

    /**
     * @Route("/supprimer/{id}", name="supprimer")
     */
    public function supprimerCategorie(Annonces $annonce, EntityManagerInterface $manager): Response
    {
        
        $manager->remove($annonce);
        $manager->flush();

        $this->addFlash('message', 'Annonce supprimée avec succès');

        return $this->redirectToRoute('admin_annonces_home');
    }
}
