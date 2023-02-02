<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Classe\Mail;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderSuccessController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/commande/merci/{stripeSessionId}', name: 'app_order_validate')]
    public function index($stripeSessionId, Cart $cart): Response
    {

        $order = $this->entityManager->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);

        if (!$order || $order->getUser() != $this->getUser()) { // si order n'existe pas OU si user.order est différent de user.session en cours
            return $this->redirectToRoute('app_home'); // tu n'as pas le droit d'accéder et tu es redirigé
        }

        if (!$order->isIsPaid()){ // modifier le statut isPaid à 1 SAUF s'il est deja à 1
            // vider la session cart
            $cart->remove();
            // Modifier le statut isPaid de la commande en mettant 1. Systeme de webhook via stripe avec "ok le paiement est bien passé", donc tu peux appeler une URL
            $order->setIsPaid(1);
            $this->entityManager->flush();
            // envoyer un mail au client pour lui confirmer sa commande suite à la validation du paiement
            $mail = new Mail(); //Envoi d'un mail au client suite inscription
            $title = "Merci pour votre commande";
            $content = "Bonjour ".$order->getUser()->getFirstname()."<br>Lorem ipsum dolor sit amet consectetur adipisicing elit. Nihil atque at, similique ipsum eos dignissimos id deleniti rerum eveniet nisi necessitatibus ab quos iure, alias reprehenderit quam cum corrupti commodi.";
            $mail->send($order->getUser()->getEmail(), $order->getUser()->getFirstname(), "Votre commande La Boutique Française est bien validée.", $title, $content);
        }
        
        return $this->render('order_success/index.html.twig', [
            // Afficher quelques information de la commande de l'utilisateur
            'order' => $order // on passe l'obhet order à la vue
        ]);
    }
}
