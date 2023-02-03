<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use App\Entity\OrderDetails;
use App\Form\OrderType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/commande', name: 'app_order')]
    public function index(Cart $cart): Response
    {
        // if (!$this->getUser()->getAddresses()->getValues()) {
        //     return $this->redirectToRoute('app_account_address_add');
        // } 
        if (!$this->getUser()->getAddresses()->getValues()) {
            return $this->redirectToRoute('app_account_address_add');
        }

        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);


        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'cart' => $cart->getFull()
        ]);
    }

    #[Route('/commande/recapitulatif', name: 'app_order_recap', methods: 'POST')]
    public function add(Cart $cart, Request $request): Response
    {

        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //$date = \new DateTime();
            $date = new DateTime(); // Attention au 'use DateTime;' au dessus
            $carriers = $form->get('carriers')->getData();
            $delivery = $form->get('addresses')->getData();

            // récup dans une variable les details du USER.
            $delivery_content = $delivery->getFirstname() . ' ' . $delivery->getLastname();
            // $delivery_content .= '<br>'.$delivery->getPhone(); ATTENTION A BIEN METTRE LE .= pour récup toutes les datas cumulées dans $delivery_content (sinon, il n'y aura que le dernier get appelé)
            $delivery_content .= '<br>' . $delivery->getPhone();

            if ($delivery->getCompagny()) {
                $delivery_content .= '<br>' . $delivery->getCompagny();
            }

            $delivery_content .= '<br>' . $delivery->getAddress();
            $delivery_content .= '<br>' . $delivery->getPostal() . ' ' . $delivery->getCity();
            $delivery_content .= '<br>' . $delivery->getCountry();


            // enregistrement de la pré-commande Order()
            $order = new Order();
            $reference = $date->format('Ymd'.'-'.uniqid());
            $order->setReference($reference);
            //dd($reference);

            $order->setUser($this->getUser());
            $order->setCreatedAt($date);
            $order->setCarrierName($carriers->getName());
            $order->setCarrierPrice($carriers->getPrice());
            $order->setDelivery($delivery_content);
            $order->setState(0);

            $this->entityManager->persist($order);


            // enregistrement des produits de la précommande OrderDetails()

            // Pour chaque nouvelle entrée je veux que tu fasse une nouvelle entrée dans OrderDetails() + fait le lien entre OrderDetails() et Order();
            foreach ($cart->getFull() as $product) {

                $orderDetails = new OrderDetails;
                $orderDetails->setMyOrder($order);
                $orderDetails->setProduct($product['product']->getName());
                $orderDetails->setQuantity($product['quantity']);
                $orderDetails->setPrice($product['product']->getPrice());
                $orderDetails->setTotal($product['product']->getPrice() * $product['quantity']);
                $this->entityManager->persist($orderDetails);


            }

            //dd($order);

            // dd($product_for_stripe); // ok, les elements du panier remontes

            // Mettre les données en BDD : persist $order + $orderDetails + flush les 2$

            $this->entityManager->flush();


            // dump($checkout_session->id);
            // dd($checkout_session);
           // dd('$date :', $date, '$carriers :', $carriers, '$delivery :', $delivery, '$delivery_content :', $delivery_content, '$order :' , $order, '$product : ', $product, '$orderDetails :', $orderDetails, 'REFERENCE', $reference); // ok, tout fonctionne

            return $this->render('order/add.html.twig', [
                'cart' => $cart->getFull(),
                'carriers' => $carriers,
                'delivery_content' => $delivery_content,
                'reference' => $reference
            ]);
            dd($order);
        };

        return $this->redirectToRoute('app_cart');
    }
}
