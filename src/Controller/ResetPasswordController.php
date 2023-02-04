<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\ResetPassword;
use App\Entity\User;
use App\Form\ResetPasswordType;
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
                $url = $this->generateUrl('app_update_password', [
                    'token' => $reset_password->getToken()
                ]);

                $content = "Bonjour ".$user->getFirstname().", <br>Vous avez demander à réinitialiser votre mot de passe sur La Boutique Française.<br><br>";
                $content .= "Merci de bien vouloir cliquer sur le lien suivant pour <a href='https://127.0.0.1:8000".$url."' class=''>mettre à jour votre mot depasse.</a>";
                $mail = new Mail();
                $mail->send($user->getEmail(), $user->getFirstname(), 'Réinitialiser votre mot de passe sur La Boutique Française.', $content);

                $this->addFlash('notice', 'Un mail contenant la procédure de réinitialisation de votre mot de passe vous parviendra dans quelques instants.');
            
            } else {

                $this->addFlash('notice', 'Cette adresse Email est inconnue.');
            }
        }

        return $this->render('reset_password/reset_password.html.twig');
    }


    #[Route('/modifier-mon-mot-de-passe/{token}', name: 'app_update_password')]
    public function update(Request $request, $token): Response
    {
        // dd($token); // ok, récupere bien le token avec l'adresse mail dans le mail envoyé
        $reset_password = $this->entityManager->getRepository(ResetPassword::class)->findOneByToken($token); // on récupere le token et le user associé

        if (!$reset_password) { // si variable n'existe pas, une erreur s'est produite donc redirection sur page reset_password
            return $this->redirectToRoute('app_reset_password');
        }

        // dd($reset_password); // ok, récupere le dernier token et le User associé

        $now = new DateTime(); // $now => dateTime courrente

        // vérifier si le createdAt = now et encore valide suivant la durée de validité
        if ($now > $reset_password->getCreatedAt()->modify('+ 3 hour')){ // $reset_password => date de la demande de réinitialisation + 3heures
            // si $now > $reset_password + 3h alors => ERREUR le token à expiré
            $this->addFlash('notice', 'Votre demande de mot de passe à expirée. Merci de la renouveller');
            return $this->redirectToRoute('app_reset_password');
        }; 
        // si $now < $reset_password + 3h alors ok on modifie
        // rendre une vue avec mot de passe et confirmer votre mot de passe

        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);
        return $this->render('reset_password/update.html.twig');

        // hash des mots de passe
        // Flush en BDD
        // redirection de l'utilisateur vers la page de connexion
        
        dd($reset_password);
        
    }


}
