<?php

namespace App\Controller;

use App\Entity\Question;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

//#[IsGranted('ROLE_ADMIN')]
class QuestionController extends AbstractController
{
    #[IsGranted('ROLE_USER')]
    #[Route('/question/new')]
    public function new(): Response
    {
//        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return new Response('new question');
    }

    #[Route('question/{id}/edit', methods: ['GET'])]
    #[ParamConverter('get', class: Question::class)]
    public function edit(Question $question): Response
    {
        $this->denyAccessUnlessGranted('EDIT', $question);
        return new Response('<h1>Имитация редактирования страницы</h1>'
            . '<br>' .
            $question->getQuestion()
        );
    }
}