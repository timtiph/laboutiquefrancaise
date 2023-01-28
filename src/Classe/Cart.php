<?php

namespace App\Classe;

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
        $session = $this->requestStack->getSession();

        $cart = $session->get('cart', []);

        if (!empty($cart[$id])) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }

        $session->set('cart',$cart); 
  


// code de base sur symfony 5
/* $cart = $this->session->get('cart', []);

    if (!empty($cart[$id])) {
       $cart[$id]++; 
    } else {
        $cart[$id] = 1;
    }

    $this->session->set('cart', $cart); */
    }

    public function get()
    {
        //return $this->session->get('cart');
        return $this->requestStack->getSession()->get('cart');
    }

    public function remove()
    {
        //return $this->session->remove('cart');
        return $this->requestStack->getSession()->remove('cart');
    }

}
