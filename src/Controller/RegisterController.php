<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    #[Route('/inscription', name: 'app_register')]

    public function index(Request $request, PersistenceManagerRegistry $doctrine, UserPasswordHasherInterface $passwordHasher ): Response
    {
        $notification = null;
        
        // Création du formulaire pour la page register

        // instense d'un nouvel obj User
        $user = new User();
        // intense création de formulaire : 2 parametres : 1. classe du formulaire  2. passer les datas
        $form = $this->createForm(RegisterType::class, $user);

        // le formulaire est capable d'écouter la requete : handleRequest = Demande de traitement
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) { // est ce que mon formulaire est soumis et est valide par rapport aux contrainte dans la class RegisterType

            $user = $form->getData(); // injecte dans obj $user toutes les données récup dans $form

            //vérifier si l'utilisateur existe déjà
            $search_email = $this->entityManager->getRepository(User::class)->findOneByEmail($user->getEmail());


            if(!$search_email) { //si la recherche est nulle alors 

                
                // Hasher le password
                $password = $passwordHasher->hashPassword($user, $user->getPassword());
                
                // dd($password); //dd = varDumpDie
                
                $user->setPassword($password);
                
                // pour enregistrer les infos dans la base avec doctrine
                $entityManager = $doctrine->getManager();
                $entityManager->persist($user);
                $entityManager->flush();

                $mail = new Mail(); //Envoi d'un mail au client suite inscription
                $title = "Bienvenu sur la premiere boutique dédiée au 100% Made in France";
                $content = "Bonjour ".$user->getFirstname()."Lorem ipsum dolor sit amet consectetur adipisicing elit. Nihil atque at, similique ipsum eos dignissimos id deleniti rerum eveniet nisi necessitatibus ab quos iure, alias reprehenderit quam cum corrupti commodi.";
                $mail->send($user->getEmail(), $user->getFirstname(), "Bienvenu sur La Boutique Française.", $title, $content);
                
                $notification = "Votre inscription s'est correctement déroulée. Vous pouvez dors et déjà vous connecter à votre compte.";
                
            } else {
                $notification = "L'email que vous avez renseigné existe déjà !!";
            }

        }

        // passer le formulaire en variable au template    
            return $this->render('register/index.html.twig', [
                'form' => $form->createView(), // $form = formulaire créé + méthode createview pour l'envoyer dans la vue ou il sera appelé
                'notification' => $notification
            ]);
    }
}
