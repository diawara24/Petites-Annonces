<?php

namespace App\Controller;

use App\Entity\Annonces;
use App\Entity\Images;
use App\Form\AnnoncesType;
use App\Form\EditProfilType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UsersController extends AbstractController
{
    /**
     * @Route("/users", name="users")
     */
    public function index(): Response
    {
        return $this->render('users/index.html.twig');
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
            // gestion des images
            $images = $formAnnonces->get('images')->getData();
            foreach($images as $image){
                // On génère un nouveau nom de fichier
                $fichier = md5(uniqid()).'.'.$image->guessExtension();

                // On copie le fichier dans le dossier uploads
                $image->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );

                // On crée l'image dans la base de données
                $img = new Images();
                $img->setName($fichier);
                $annonce->addImage($img);
            }
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

    

    /**
     * @Route("/users/profil/modifier", name="users_profil_modifier")
     */
    public function editProfil(Request $request ,EntityManagerInterface $manager): Response
    {
        $user = $this->getUser();
        
        $formEditUser = $this->createForm(EditProfilType::class, $user);
        $formEditUser->handleRequest($request);

        if($formEditUser->isSubmitted() && $formEditUser->isValid()){
            $manager->persist($user);

            $manager->flush();

            $this->addFlash('message','Profil mis à jour');
            return $this->redirectToRoute("users");
        }
        return $this->render('users/editProfil.html.twig', [
            'formEditUser' => $formEditUser->createView()
        ]);
    }

    /**
     * Undocumented function
     *@Route("/users/profil/editPassword",name="users_profil_editPassword")
     */
    public function editPasswrd(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $passwordEncoder):Response
    {
        
        if($request->isMethod('post')){
            $user = $this->getUser();

            if($request->request->get('pass') == $request->request->get('pass1')){
                $user->setPassword($passwordEncoder->encodePassword($user,$request->request->get('pass')));
                $manager->flush();
                $this->addFlash('message','mot de pass mis à jour avec succès');

                return $this->redirectToRoute('users');

            }else{
                $this->addFlash('message','les deux mot de passe ne sont pas identitque');
            }
        }


        return $this->render('users/editPassword.html.twig');
    }


}
