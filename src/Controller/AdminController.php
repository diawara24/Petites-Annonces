<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Form\CategoriesType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
/**
 * @Route("/admin", name="admin_")
 * Undocumented class
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/categories/ajout", name="categories_ajout")
     */
    public function ajoutCategorie(Request $request, EntityManagerInterface $manager): Response
    {
        $categorie = new Categories();
        $form = $this->createForm(CategoriesType::class, $categorie);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($categorie);

            $manager->flush();

            return $this->redirectToRoute("admin_home");

        }
        return $this->render('admin/categories/ajout.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
