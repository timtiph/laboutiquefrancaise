<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use App\Entity\Product;
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
    public function index($reference, )// : Response
    {
        //dd($reference);
        $product_for_stripe = []; // array pour récup les details du panier
        $MY_DOMAIN = 'https://127.0.0.1:8000'; // var nom de domaine

        $order = $this->entityManager->getRepository(Order::class)->findOneByReference($reference); //je lui demande de me trouver en BDD l'enregistrement par sa référence
        
        if(!$reference){ # si la référence n'existe pas, alors order n'existe pas
            // new JsonResponse(['error'=>'order']);
            return $this->redirectToRoute('app_order');

        } else {

            foreach ($order->getOrderDetails()->getValues() as $product) {
                $product_object = $this->entityManager->getRepository(Product::class)->findOneByName($product->getProduct()); //création objet product pour récup toutes les infos
                $product_for_stripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $product->getPrice(),
                    'product_data' => [
                        'name' => $product->getProduct(),
                        'images' => [$MY_DOMAIN."/uploads/".$product_object->getIllustration()]
                        
                    ],
                ],
                'quantity' => $product->getQuantity(),
            ];

        }
            $product_for_stripe[] = [
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => $order->getCarrierPrice() * 100,
                'product_data' => [
                    'name' => $order->getCarrierName(),
                    'images' => [$MY_DOMAIN],
                    ],
                ],
            'quantity' => 1,
            ];

        Stripe::setApiKey('sk_test_51MVySHKFjf7KItaigS7ZDPnSrxJOnEm7wzeGLjuNY2cUp06if1NxXclOXlZJwogXeb84papT2ra2ZRmwieaYL89X0042laGGeo');
        //header('Content-Type: application/json');
        
        
        $checkout_session = Session::create([
            'customer_email' => $this->getUser()->getEmail(),
            'line_items' => [
                $product_for_stripe
            ],
            'mode' => 'payment', // paiement 1 shot
            'success_url' => $MY_DOMAIN . '/commande/merci/{CHECKOUT_SESSION_ID}', // {CHECKOUT_SESSION_ID} permet de récup le detail de la commande depuis stripe pour le merci (ou le erreur)
            'cancel_url' => $MY_DOMAIN . '/commande/erreur/{CHECKOUT_SESSION_ID}',
        ]);

        $order->setStripeSessionId($checkout_session->id);

        $this->entityManager->flush();
        
        // Renvoyer une réponse en JSON
        // $response = new JsonResponse(['id'=>$checkout_session->id]);
        // return $response;
        
        // dd($checkout_session->url); ok ça fonctionne
        
        // header("HTTP/1.1 303 See Other");
        // header("Location: " . $checkout_session->url);
        
        return $this->redirect($checkout_session->url);
        
        }   
    
    
    }
}
