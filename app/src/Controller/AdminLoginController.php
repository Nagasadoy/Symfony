<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminLoginController extends AbstractController
{
    #[IsGranted('PUBLIC_ACCESS')]
    #[Route('/admin/login', name: 'admin_login')]
    public function login()
    {
        return new Response('форма логина для админов');
    }
}