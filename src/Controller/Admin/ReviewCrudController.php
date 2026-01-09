<?php

namespace App\Controller\Admin;

use App\Entity\Review;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;

class ReviewCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Review::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            AssociationField::new('event', 'Événement'),
            AssociationField::new('author', 'Auteur'),
            IntegerField::new('rating', 'Note (1-5)'),
            TextEditorField::new('content', 'Contenu'),
            BooleanField::new('isValidated', 'Validé')
                ->setHelp('Cochez pour rendre l\'avis visible aux autres utilisateurs'),
            TextEditorField::new('comments', 'Commentaires de modération')
                ->setHelp('Réservé aux modérateurs'),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Avis')
            ->setEntityLabelInPlural('Avis')
            ->setPageTitle('index', 'Modération des avis')
            ->setPageTitle('edit', 'Modérer un avis')
            ->setDefaultSort(['isValidated' => 'ASC', 'id' => 'DESC'])
            ->showEntityActionsInlined();
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(BooleanFilter::new('isValidated', 'Statut'))
            ->add(EntityFilter::new('event', 'Événement'))
            ->add(EntityFilter::new('author', 'Auteur'));
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->update(Crud::PAGE_INDEX, Action::EDIT, fn(Action $action) => $action->setLabel('✏️ Modérer'));
    }
}
