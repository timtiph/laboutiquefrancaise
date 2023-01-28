<?php

namespace App\Classe;
// Code Symfony 5

use Symfony\Component\HttpFoundation\RequestStack;
//use Symfony\Component\HttpFoundation\Session\SessionInterface;


class Cart

{
    //private $session;
    private $requestStack;

    //public function __construct(SessionInterface $session)
    public function __construct(RequestStack $requestStack)
    {
        //$this->session = $session;
        $this->requestStack = $requestStack;
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
}
