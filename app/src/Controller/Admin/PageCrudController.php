<?php

namespace App\Controller\Admin;

use App\Entity\Page;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PageCrudController extends AbstractCrudController
{

    public static function getEntityFqcn(): string
    {
        return Page::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            NumberField::new('id')->hideOnForm(),
            NumberField::new('pageNumber', 'Номер страницы'),
            TextField::new('text', "Текст"),
            AssociationField::new('book', "Книга")->setRequired(false),
        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        $view = Action::new('Book', 'viewBook')
            ->linkToCrudAction('index')
            ->displayIf(static function ($entity) {
                return $entity->getPageNumber() === 1;
            });
        $actions->add(Crud::PAGE_INDEX, $view);
        return parent::configureActions($actions);
    }
}