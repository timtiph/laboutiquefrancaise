<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
        /**
     * Récapitulatif panier
     */
    #[Route('/mon-panier', name: 'app_cart')]

    public function index(): Response
    {
        return $this->render('cart/index.html.twig');
    }
    
    
    
    /**
     * Ajout au panier
     * @param $id
     */
    #[Route('/cart/add/{id}', name: 'add_to_cart')]


    public function add($id)  
    {
        dd($id);
        return $this->render('cart/index.html.twig');
    }
}
