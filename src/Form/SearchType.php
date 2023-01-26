<?php

namespace App\Form;

use App\Classe\Search;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchType extends AbstractType
{

    /**
     * @return 
     */
    // création du formulaire avec buildForm
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('string', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Votre recherche ....',
                    'class' => 'form-control-sm'
                ]
            ])
            ->add('categories', EntityType::class, [
                'label' => false,
                'required' => false, 
                'class' => Category::class,
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Filtrer',
                'attr' => [
                    'class' => 'btn-block btn-info'
                ]
            ])
            ;
    }


    // creation de function permettant la configuration d'options du formulaire

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Search::class,
            'methode' => 'GET',
            'crsf protection' => false,

        ]);
    }

    public function getBlockPrefix()
    {
        // retourne un tableau avec des valeurs. le tableau est préfixé du nom de la classe "Search" qui sera visible dans l'URL
        // si nous ne voulons pas que la classe soit dans l'URL, return '';
        return '';
        
    }

}