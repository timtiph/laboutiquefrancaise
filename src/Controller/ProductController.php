<?php

namespace App\Controller;

use App\Classe\Search;
use App\Entity\Product;
use App\Form\SearchType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    //injection de dependance : tu prends l'entityManager pour entrer dans ce controller
    // Appel entityManager pour récup tous les produits
    private $entityManager;

    // intialise un constructeur
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }


    #[Route('/nos-produits', name: 'app_products')]

    public function index(Request $request): Response
    {
        //pour aller chercher de data : utiliser le repository associé => product repository (fichier permettant l'acces rapide aux données)
        $products = $this->entityManager->getRepository(Product::class)->findAll();
        // dd($products); => $products est chargée grace à findAll() qui est égal à "SELECT * FROM product"</g>


        $search = new Search;
        $form = $this->createForm(SearchType::class, $search);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $products = $this->entityManager->getRepository(Product::class)->findWithSearch($search);
        }

        return $this->render('product/index.html.twig', [
            'products' => $products, // passer tous les produits à twig
            'form' => $form->createView()
        ]);
    }

    // fonction permettant de rentrer dans le produit avec un clic
    // dans la mesure ou cette fonction sera directement liée au produit, il est possible de créer une 2eme fonction dans le ProductController (cf SEGMENTATION)

    #[Route('/produit/{slug}', name: 'app_product')] // {ajout du slug} pour affichage d'une URL propre avec /produit/nom-du-produit

    public function show($slug): Response
    {
        // dd($slug); // récup le slug de l'URL

        //pour aller chercher de data : utiliser le repository associé => product repository (fichier permettant l'acces rapide aux données)
        $product = $this->entityManager->getRepository(Product::class)->findOneBySlug($slug);
        // dd($product); //=> $product est chargée grace à findOneBySlug() qui est égal à "SELECT * FROM product WHERE slug = $slug"</g>

        $products = $this->entityManager->getRepository(Product::class)->findByIsBest(1); // Passé les produits best à la vue Show

        if(!$product) { // si tu ne trouve pas de produit, redirect to app_products
            return $this->redirectToRoute('app_products');
        }

        return $this->render('product/show.html.twig', [
            'product' => $product, // passer tous les produits à twig
            'products' => $products, // passer les produits isBest à twig
        ]);
    }
}
