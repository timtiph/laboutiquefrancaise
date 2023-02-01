<?php

namespace App\Controller;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderCancelController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    #[Route('/commande/erreur/{stripeSessionId}', name: 'app_order_cancel')]
    public function index($stripeSessionId): Response
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);

        if (!$order || $order->getUser() != $this->getUser()) { // si order n'existe pas OU si user.order est différent de user.session en cours
            return $this->redirectToRoute('app_home'); // tu n'as pas le droit d'accéder et tu es redirigé
        }

        // envoyer un email echec de paiement


        return $this->render('order_cancel/index.html.twig', [
            'order' => $order
        ]);
    }
}
