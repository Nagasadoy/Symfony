<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class ApiLoginController extends AbstractController
{
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function index(#[CurrentUser] ?User $user): Response
    {
        if (null === $user) {
            return $this->json([
                'message' => 'неверные данные'
            ], Response::HTTP_UNAUTHORIZED);
        }


        return $this->json([
            'user' => $user->getUserIdentifier()
        ]);
    }

//    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
//    public function index(/*#[CurrentUser] ?User $user*/ Request $request): Response
//    {
//        $user = $this->getUser();
//        if (null === $user) {
//            return $this->json([
//                'message' => 'missing credentials',
//            ], Response::HTTP_UNAUTHORIZED);
//        }
//
//        $token = '123123';
//
//          return $this->json([
//              'message' => 'Welcome to your new controller!',
//              'path' => 'src/Controller/ApiLoginController.php',
//              'user' => $user->getUserIdentifier(),
//              'token' => $token,
//          ]);
//      }
}
