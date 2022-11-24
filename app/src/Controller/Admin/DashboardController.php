<?php

namespace App\Controller\Admin;

use App\Entity\Book;
use App\Entity\Page;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
//        return parent::index();
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);

        // Option 1. Make your dashboard redirect to the same page for all users
        return $this->redirect($adminUrlGenerator->setController(BookCrudController::class)->generateUrl());

    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Книги и страницы');
    }

    public function configureMenuItems(): iterable
    {
        return [
            MenuItem::section('Основное'),
            MenuItem::linkToCrud('Книги', 'fa fa-home', Book::class),
            MenuItem::linkToCrud('Страницы', 'fa fa-home', Page::class),
            MenuItem::section('Поиск'),
            MenuItem::linkToUrl('Search in Google', 'fab fa-google', 'https://google.com'),
        ];
    }
}
