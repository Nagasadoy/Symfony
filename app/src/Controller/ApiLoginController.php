<?php

namespace App\Controller;

use App\Entity\User;
use DomainException;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationFailureHandler;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Http\Authentication\AuthenticationSuccessHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class ApiLoginController extends AbstractController
{
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function index(
        #[CurrentUser] ?User $user,
        AuthenticationSuccessHandler $successHandler,
        AuthenticationFailureHandler $failureHandler,
        Request $request,
    ): Response
    {
        if (null === $user) {
            return $this->json([
                'message' => 'неверные данные'
            ], Response::HTTP_UNAUTHORIZED);
        }

        try {
            return $successHandler->handleAuthenticationSuccess($user);
        } catch (DomainException $exception) {
            return $failureHandler->onAuthenticationFailure($request, new AuthenticationException());
        }
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
