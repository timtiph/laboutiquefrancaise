<?php

namespace App\Classe;
// Code Symfony 5

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
//use Symfony\Component\HttpFoundation\Session\SessionInterface;


class Cart

{
    //private $session;
    private $requestStack;
    private $entityManager;

    //public function __construct(SessionInterface $session)
    public function __construct(EntityManagerInterface $entityManager, RequestStack $requestStack)
    {
        //$this->session = $session;
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
    }


    public function add($id)
    {

        // $cart = $this->session->get('cart', []);
        // if (!empty($cart[$id])) {
        //     $cart[$id]++;
        // } else {
        //     $cart[$id] = 1;
        // }
        // $this->session->set('cart', $cart);


        // récup la session en cours
        // $session = $this->getSession();
        $session = $this->requestStack->getSession();

        // récup l'objet cart dans un tableau, sinon renvoi un tableau vide
        $cart = $session->get('cart', []);

        // si acticle deja au panier ajoute 1 sinon mettre 1
        if (!empty($cart[$id])) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }

        // la session renvoi le tableau incrémenté
        $session->set('cart', $cart);
    }

    public function get()
    {
        // récupere la session
        //return $this->session->get('cart');
        return $this->requestStack->getSession()->get('cart');
    }

    public function remove()
    {
        //remove la session
        //return $this->session->remove('cart');
        return $this->requestStack->getSession()->remove('cart');
    }

    public function delete($id)
    {
        // récup la session en cours
        $session = $this->requestStack->getSession();

        // récup l'objet cart dans un tableau, sinon renvoi un tableau vide
        $cart = $session->get('cart', []);

        // on eleve l'entrée dans $cart qui a l'id $id
        unset($cart[$id]);

        return $this->requestStack->getSession()->set('cart', $cart);
    }

    /**
     * @id du produit
     * function pour retirer une quantité à un produit sur la page templates/cart/index.html.twig
     */

    public function decrease($id)
    {
        // vérifier si quantity = 1 > si oui, produit à supprimer 
        // récup la session en cours
        $session = $this->requestStack->getSession();

        // récup l'objet cart
        $cart = $session->get('cart', []);

        if ($cart[$id] > 1) {
            // retirer une quantité (-1)
            $cart[$id]--;
        } else {
            // supprimer le produit
            unset($cart[$id]);
        }

        return $this->requestStack->getSession()->set('cart', $cart);
    }

    public function getFull()
    {
        $cartComplete = [];

        if ($this->get()) {

            // Enrichir le cartComplete de datas pris par Doctrine en BDD
            // as $id(clé) => $qunatity(valeur)
            foreach ($this->get() as $id => $quantity) {

                $product_object = $this->entityManager->getRepository(Product::class)->findOneById($id);
                // a chaque itération, injete dans $cartComplete[] une nouvelle entrée avec plusieurs parametres
                
                // si on essai de passer un id qui n'est pas dans la base
                // suppression du produit non existant de la session cart
                if (!$product_object) {
                    $this->delete($id);
                    continue;
                }
                
                $cartComplete[] = [

                    // produit venant de la BDD via Doctrine et EntityManager intialisé par __construct, recherche du produit par $id produit
                    'product' => $product_object,
                    'quantity' => $quantity
                ];
            }
        }

        return $cartComplete;
    }
}
