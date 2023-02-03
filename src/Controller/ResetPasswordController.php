<?php

namespace App\Controller;

use App\Entity\ResetPassword;
use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResetPasswordController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    #[Route('/mot-de-passe-oublie', name: 'app_reset_password')]
    public function index(Request $request): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        if ($request->get('email')) {
            // dd($request->get('email')); // ok, récup l'adresse mail saisie
            $user = $this->entityManager->getRepository(User::class)->findOneByEmail($request->get('email')); // recherche si le mail est présent dans la BDD
            // dd($user); // ok, récup le User

            if ($user) { // si l'utilisateur est présent dans la BDD, 
                
                // I : Enregistrer en BDD la demande de reset_password avec le user, token, createdAt

                $reset_password = new ResetPassword(); // 1 : se servir de l'entity qui vient d'être crée : ResetPassword et initialiser un new
                $reset_password->setUser($user); // on injecte dedans le $user qui vient d'etre trouvé
                $reset_password->setToken(uniqid()); // 2 : on injecte un unique id dans le token
                $reset_password->setCreatedAt(new DateTime()); // 3 : mettre une new DateTime
                $this->entityManager->persist($reset_password); // 4 : appeler EntityManager, persister l'obj reset_password, 
                $this->entityManager->flush(); // 5 : flush le tout

                // II : Envoyer un mail à l'utilisateur avec un lien lui permettant de mettre à jour son mot de passe 
            }
        }

        return $this->render('reset_password/reset_password.html.twig');
    }
}
