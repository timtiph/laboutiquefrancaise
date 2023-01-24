<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AccountPasswordController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/compte/modifier-mon-mot-de-passe', name: 'app_account_password')]

    public function index(Request $request, PersistenceManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher): Response // Mettre la request (1) dans une var $request, puis handleRequest($request) ecoute le formulaire.
    {

        $notification = null;
        $user = $this->getUser(); // récupérer les données de l'objet en cours

        // dd($user); // user récupéré

        $form = $this->createForm(ChangePasswordType::class, $user); // 1er param : class ChangePasswordType, 2eme arg : la class à laquelle doit etre rattaché le form


        $form->handleRequest($request); //handleRequest($request) ecoute le formulaire

        if ($form->isSubmitted() && $form->isValid()) {

            $old_pwd = $form->get('old_password')->getData();
            // dd($old_pwd);
            $user = $form->getData(); // injecte dans obj $user toutes les données récup dans $form


            if (!$passwordHasher->isPasswordValid($user, $old_pwd)) {
                $notification = "Votre mot de passe actuel n'est pas le bon!";
                return $this->redirectToRoute('app_account_password');
            } else {
                
                // dd($user);
                
                $new_pwd = $form->get('new_password')->getData();
                $user->setPassword($passwordHasher->hashPassword($user, $new_pwd));
                $entityManager = $doctrine->getManager();
                // $entityManager->persist($user); // Methode persist = fige la donnée et crée la. Inutile ici puisque deja créée
                $entityManager->flush();
                $notification = "Votre mot de passe à bien été mis à jour!";
            }
        }
        
        

        return $this->render('account/password.html.twig', [
            'form' => $form->createView(), // passer la vue form à twig
            'notification' => $notification,
        ]);
    }
}
