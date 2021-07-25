<?php

namespace App\Controller;

use App\Entity\Annonces;
use App\Form\AnnoncesType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UsersController extends AbstractController
{
    /**
     * @Route("/users", name="users")
     */
    public function index(): Response
    {
        return $this->render('users/index.html.twig', [
            'controller_name' => 'UsersController',
        ]);
    }

    /**
     * @Route("/users/annonces/ajout", name="users_annonces_ajout")
     */
    public function ajoutAnnonce(Request $request, EntityManagerInterface $manager): Response
    {
        $annonce = new Annonces();
        $formAnnonces = $this->createForm(AnnoncesType::class, $annonce);
        $formAnnonces->handleRequest($request);

        if($formAnnonces->isSubmitted() && $formAnnonces->isValid()){
            $annonce->setUsers($this->getUser());
            $annonce->setActive(false);
            
            $manager->persist($annonce);

            $manager->flush();

            return $this->redirectToRoute("admin_home");
        }
        return $this->render('users/annonces/ajout.html.twig', [
            'formAnnonces' => $formAnnonces->createView()
        ]);
    }


}
