<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class StripeController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/commande/create-session/{reference}', name: 'app_stripe_create_session')]
    public function index(Cart $cart, $reference)// : Response
    {
        dd($reference);
        $product_for_stripe = []; // array pour récup les details du panier
        $MY_DOMAIN = 'https://127.0.0.1:8000'; // var nom de domaine

        $order = $this->entityManager->getRepository(Order::class)->findOneByReference($reference); #je lui demande de me trouver en BDD l'enregistrement par sa référence
        
        dd($order);

        if(!$order){ # si la référence n'existe pas, alors order n'existe pas
            // new JsonResponse(['error'=>'order']);
            return $this->redirectToRoute('app_order');

        } else {

            
            
            foreach ($cart->getFull() as $product) {
                
                $product_for_stripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $product['product']->getPrice(),
                    'product_data' => [
                        'name' => $product['product']->getName(),
                        'images' => [$MY_DOMAIN."/uploads/".$product['product']->getIllustration()]
                        
                    ],
                ],
                'quantity' => $product['quantity'],
            ];
            
        }
        
        
        Stripe::setApiKey('sk_test_51MVySHKFjf7KItaigS7ZDPnSrxJOnEm7wzeGLjuNY2cUp06if1NxXclOXlZJwogXeb84papT2ra2ZRmwieaYL89X0042laGGeo');
        //header('Content-Type: application/json');
        
        
        $checkout_session = Session::create([
            'line_items' => [
                $product_for_stripe
            ],
            'mode' => 'payment', // paiement 1 shot
            'success_url' => $MY_DOMAIN . '/success.html',
            'cancel_url' => $MY_DOMAIN . '/cancel.html',
        ]);
        
        // Renvoyer une réponse en JSON
        // $response = new JsonResponse(['id'=>$checkout_session->id]);
        // return $response;
        // dd($checkout_session->url);
        
        // header("HTTP/1.1 303 See Other");
        // header("Location: " . $checkout_session->url);
        
        //$url_strip = $checkout_session->url;
        return $this->redirect($checkout_session->url);
        
        }   
        
        
    }
}
