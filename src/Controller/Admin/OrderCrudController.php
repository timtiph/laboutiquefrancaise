<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

class OrderCrudController extends AbstractCrudController
{
    private $entityManager;
    private $adminUrlGenerator;
    
    public function __construct(EntityManagerInterface $entityManager, AdminUrlGenerator $adminUrlGenerator)
    {
        $this->entityManager = $entityManager;
        $this->adminUrlGenerator = $adminUrlGenerator;    
    }

    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $updatePreparation = Action::new('updatePreparation', 'Préparation en cours', 'fas fa-box-open')->linkToCrudAction('updatePreparation');
        $updateDelivery = Action::new('updateDelivery', 'Livraison en cours', 'fas fa-truck')->linkToCrudAction('updateDelivery');

        return $actions
            ->add('detail', $updatePreparation)
            ->add('detail', $updateDelivery)
            ->add('index', 'detail');
    }

    public function updatePreparation(AdminContext $context)
    {
        $order = $context->getEntity()->getInstance();
        $order->setState(2);
        $this->entityManager->flush();

        $this->addFlash('notice', "<span class='text-center' style='color:darkgreen;'>La commande : <br> <strong>".$order->getReference()."</strong><br> est bien <strong><u>en cours de PRÉPARATION.</u></strong></span>");

        $url = $this->adminUrlGenerator
        ->setController(OrderCrudController::class)
        ->setAction(Action::INDEX)
        ->generateUrl();

        // envoi d'un mail au client pour le suivi du statut de sa commande :
        // $mail = new Mail();
        // $mail->send($order->getUser()->getEmail()); .... etc voir le src/Controller/OrderSuccessController.php pour la rédaction de la function

        return $this->redirect($url);
    }

    public function updateDelivery(AdminContext $context)
    {
        $order = $context->getEntity()->getInstance();
        $order->setState(3);
        $this->entityManager->flush();

        $this->addFlash('notice', "<span class='text-center' style='color:orange;'>La commande : <br><strong>".$order->getReference()."</strong> est bien <br><strong><u>en cours de LIVRAISON.</u></strong></span>");

        $url = $this->adminUrlGenerator
        ->setController(OrderCrudController::class)
        ->setAction(Action::INDEX)
        ->generateUrl();

        return $this->redirect($url);
    }

    public function configureCrud(Crud $crud) :Crud
    {
        return $crud->setDefaultSort(['id' => 'DESC']);
    }


    // Personnaliser les colones dans EasyAdmin

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            DateTimeField::new('createdAt', 'Passé le'),
            TextField::new('user.getFullName', 'Prénom Nom'),
            TextField::new('delivery', 'Adresse de livraison')->onlyOnDetail()->renderAsHtml(), //TextEditorField ne fonctionne pas pour l'interpretation HTML => TextField + ->renderAsHtml()
            MoneyField::new('total', 'Total Produit')->setCurrency('EUR'),
            TextField::new('carrierName', 'Transporteur'),
            MoneyField::new('carrierPrice', 'Total Frais de port')->setCurrency('EUR'),
            ChoiceField::new('state')->setChoices([
                'non payée' => 0,
                'payée' => 1,
                'préparation en cours' => 2,
                'livraison en cours' => 3
            ]),
            ArrayField::new('orderDetails', 'Produits achetés')->hideOnIndex(),

        ];
    }

}
