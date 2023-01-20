<?php

namespace App\Controller;

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
        // Création du formulaire pour la page register

        // instense d'un nouvel obj User
        $user = new User();
        // intense création de formulaire : 2 parametres : 1. classe du formulaire  2. passer les datas
        $form = $this->createForm(RegisterType::class, $user);

        // le formulaire est capable d'écouter la requete : handleRequest = Demande de traitement
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) { // est ce que mon formulaire est soumis et est valide par rapport aux contrainte dans la class RegisterType

            $user = $form->getData(); // injecte dans obj $user toutes les données récup dans $form

            // Hasher le password
            $password = $passwordHasher->hashPassword($user, $user->getPassword());

            // dd($password); //dd = varDumpDie
            
            $user->setPassword($password);

            // pour enregistrer les infos dans la base avec doctrine
            $entityManager = $doctrine->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

        }

        // passer le formulaire en variable au template    
            return $this->render('register/index.html.twig', [
                'form' => $form->createView() // $form = formulaire créé + méthode createview pour l'envoyer dans la vue ou il sera appelé
            ]);
    }
}
