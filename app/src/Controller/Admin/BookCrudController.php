<?php

namespace App\Controller\Admin;


use App\Entity\Book;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class BookCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Book::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            NumberField::new('id')->hideOnForm(),
            TextField::new('name', "Название"),
        ];
    }

    public function configureFilters(Filters $filters): Filters
    {
        $filters->add('name');
        return parent::configureFilters($filters);
    }

    public function configureCrud(Crud $crud): Crud
    {
        $crud->setEntityLabelInSingular("Книга");
        $crud->setEntityLabelInPlural("Книги");
        $crud->setPageTitle(Crud::PAGE_INDEX, 'Книги');
        $crud->setPageTitle(Crud::PAGE_NEW, 'Создать книгу');
        $crud->setDefaultSort(['name' => 'ASC']);
        return parent::configureCrud($crud);
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions->update(
            Crud::PAGE_INDEX,
            Action::NEW,
            function (Action $action) {
                return $action->setLabel("Создать книгу");
            }
        );
        return $actions;
    }

    public function createEntity(string $entityFqcn)
    {
        return new $entityFqcn();
    }
}