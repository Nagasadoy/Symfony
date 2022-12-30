<?php

namespace App\EventListener;

use App\Entity\User;
use App\Security\AccountNotVerifiedAuthenticationException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Event\CheckPassportEvent;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

class CheckVerifiedUserSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return [
            CheckPassportEvent::class => ['onCheckPassport', -10]
        ];
    }

    public function onCheckPassport(CheckPassportEvent $event)
    {
        $passport = $event->getPassport();
//        dd($event);
        $user = $passport->getUser();
        if(!$user instanceof User) {
            throw new \Exception('Неожиданный тип пользователя');

        }

        if (!$user->isIsVerified()) {
//            throw new CustomUserMessageAuthenticationException(
//                'Пожалуйста верифицируйтесь перед входом в систему'
//            );
            throw new AccountNotVerifiedAuthenticationException();
        }
    }
}