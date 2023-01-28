<?php

namespace App\Controller;

use App\Classe\Cart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    /**
     * Récapitulatif panier
    */

    #[Route('/mon-panier', name: 'app_cart')]

    public function index(Cart $cart): Response
    {
    
        return $this->render('cart/index.html.twig', [
        // on insert dans la vue le tableau $cart->get() du panier avec le couple id.produit + quantity
            'cart' => $cart->get()
    ]);
    }
    
    
    
    /**
     * Ajout au panier
     * @param $id
    */

    #[Route('/cart/add/{id}', name: 'add_to_cart')]


    public function add(Cart $cart, $id)  
    {
        $cart->add($id);

        return $this->redirectToRoute('app_cart');
    }

    
    /**
     * Vider le panier
    */

    #[Route('/cart/remove', name: 'remove_my_cart')]


    public function remove(Cart $cart)  
    {
        
        $cart->remove();
        
        return $this->redirectToRoute('app_products');

    }
}
