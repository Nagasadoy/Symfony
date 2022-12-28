<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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
}