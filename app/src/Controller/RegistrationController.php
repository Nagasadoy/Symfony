<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{

    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    #[Route('/registration', name: 'registration', methods: ['POST'])]
    public function registration(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $content = $request->toArray();

        $email = $content['email'];
        $plaintextPassword = $content['password'];

        $user = new User();

        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $plaintextPassword
        );

        $user->setPassword($hashedPassword);
        $user->setEmail($email);

        $user->setRoles(['ROLE_ADMIN']);

        $this->userRepository->save($user, flush: true);


        return $this->json([
            'user' => $user
        ]);
    }
}