<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    #[Route('/admin/comments')]
    public function comment(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_COMMENT_ADMIN');
        return new Response('Комментарии для администратора');
    }
}