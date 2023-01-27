<?php

namespace App\Classe;

use App\Entity\Category;

class Search
{
// les propriétés de cette classe sont en public car si le private m'obligerai à générer des get et set qui rendrais cette classe beaucoup plus lourde qu'avec les propriétés public
// mettre en private avec des get et set n'est pas important et ne nuit pas au fonctionnement de l'application 


    /**
     * @var string
    */

    public $string = '';


    /**
     * @var Category[]
    */

    public $categories = [];

}