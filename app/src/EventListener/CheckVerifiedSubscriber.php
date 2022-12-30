<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;

class CheckVerifiedSubscriber implements EventSubscriberInterface
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public static function getSubscribedEvents()
    {
        return [
            LoginFailureEvent::class => 'onLoginFailure'
        ];
    }

    public function onLoginFailure(LoginFailureEvent $event)
    {
        $response = new RedirectResponse(
            $this->router->generate('app_login')
        );

        $event->setResponse($response);
    }
}