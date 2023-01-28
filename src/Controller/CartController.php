<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    
    private $entityManager;
    
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Récapitulatif panier
    */

    #[Route('/mon-panier', name: 'app_cart')]

    public function index(Cart $cart): Response
    {
        // code avant création de contion getFull();
        // // Panier complet
        // $cartComplete = [];

        // if($cart->get()){

            
        //     // Enrichir le cartComplete de datas pris par Doctrine en BDD
        //     // as $id(clé) => $qunatity(valeur)
        //     foreach ($cart->get() as $id => $quantity) {
        //         // a chaque itération, injete dans $cartComplete[] une nouvelle entrée avec plusieurs parametres
        //         $cartComplete[] = [
        //             // produit venant de la BDD via Doctrine et EntityManager intialisé par __construct, recherche du produit par $id produit
        //             'product' => $this->entityManager->getRepository(Product::class)->findOneById($id), 
        //             'quantity' => $quantity
        //         ];
        //     }
        // }

        return $this->render('cart/index.html.twig', [
        // on insert dans la vue le tableau $cart->get() du panier avec le couple id.produit + quantity
            //'cart' => $cart->get()

            // passer à la vue le tableau généré dans $cartComplete
            'cart' => $cart->getFull()
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

    /**
     * retirer une sorte d'article du panier
    */

    #[Route('/cart/delete/{id}', name: 'delete_to_cart')]


    public function delete(Cart $cart, $id)  
    {
        
        $cart->delete($id);
        
        return $this->redirectToRoute('app_cart');

    }

    /**
     * retirer une quantité d'article du panier
    */

    #[Route('/cart/decrease/{id}', name: 'decrease_to_cart')]


    public function decrease(Cart $cart, $id)  
    {
        
        $cart->decrease($id);
        
        return $this->redirectToRoute('app_cart');

    }
}
