<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Page;
use App\Form\PageType;
use App\Repository\PageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FormController extends AbstractController
{

    public function __construct(private readonly PageRepository $pageRepository)
    {
    }

    #[Route('/ok/{id}', name: 'pageLoad')]
    public function pageLoad($id): Response
    {
        $page = $this->pageRepository->find($id);
        return $this->json(['ok' => $page]);
    }

    #[Route('/')]
    public function newForm(Request $request): Response
    {
        $page = new Page();
        $page->setPageNumber(11);
        $page->setText('fsdfsdfs');

        $form = $this->createForm(PageType::class, $page);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $page = $form->getData();
            $this->pageRepository->save($page, true);
            return $this->redirectToRoute('pageLoad', ['id' => $page->getId()]);
        }

        return $this->renderForm('page/page.html.twig', ['form' => $form]);
    }

}