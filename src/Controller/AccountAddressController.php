<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountAddressController extends AbstractController
{
    #[Route('/compte/mes-adresses', name: 'app_account_address')]
    public function index(): Response
    {
        return $this->render('account/address.html.twig');
    }
    
    #[Route('/compte/ajouter-une-adresse', name: 'app_account_address_add')]
    public function add(): Response
    {
        return $this->render('account/address_add.html.twig');
    }
    
}
