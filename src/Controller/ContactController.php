<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/nous-contacter', name: 'app_contact')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $this->addFlash('notice', 'Merci de nous avoir contacter, notre équipe va vous répondre dans les meilleurs délais');

            // traitement de la demande de contact :
                    // 1. soit un stockage en BDD
                    // 2. soit envoi d'un mail aux admin avec le message du mail $content
                    $email = $form->get('email')->getData();
                    $prenom = $form->get('prenom')->getData();
                    $nom = $form->get('nom')->getData();
                    $message = $form->get('content')->getData();
                
                    
                    
                    $content = "Bonjour, vous recevez un mail de : ".$prenom." ".$nom.". ";
                    $content .= "Vous pouvez lui répondre à l'adresse : ".$email.". ";
                    $content .= "Voici son message : ".$message." ";
                    $mail = new Mail;
                    $mail->send('ugoblackandwhite@gmail.com', 'Administrateur', 'Demande de contact', $content); // mettre l'adresse de la personne recevant les mails demande de contact
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form
        ]);
    }
}
