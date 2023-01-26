<?php
// Classe recherche  pour le filtrage
// création propriété en fonction de ce qui doit etre afficher dans la colonne filtre

namespace App\Classe;

use App\Entity\Category;

class Search 
{
    // recherche article par le nom

    //PHPDoc 
    /**
     * @var string valeur par défault format string
     */   

    public $string = ''; // valeur par default vide


    /**
     * @var Category[] prend en compte un array contenant les catégories 
     */

    public $categories = [  // represente l'essemble des categories (celles selectionnées pour faire le recherche)

    ]; 
    
}