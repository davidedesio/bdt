<?php

namespace App\EventListener;

use App\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserStatusListener
{
    private $tokenStorage;
    private $router;

    public function __construct(TokenStorageInterface $tokenStorage, RouterInterface $router) {
        $this->tokenStorage = $tokenStorage;
        $this->router = $router;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        if (!$token = $this->tokenStorage->getToken()) {
            return;
        }

        if (!$token->isAuthenticated()) {
            return;
        }

        if (!$user = $token->getUser()) {
            return;
        }

        if(!$user instanceof User){
            return;
        }

        // update user
        $status = $user->getStatus();

        if($user->getDel()==1 || $status==2){

            $route = 'home';

            if ($route === $event->getRequest()->get('_route')) {
                return;
            }

            $url = $this->router->generate($route);

            $response = new RedirectResponse($url);
            $this->tokenStorage->setToken(null);

            return $event->setResponse($response);

        } else if(is_null($status) || $status==0){

            $route = 'app_registered';

            if ($route === $event->getRequest()->get('_route')) {
                return;
            }

            $url = $this->router->generate($route,['tbc'=>true]);

            $response = new RedirectResponse($url);
            $this->tokenStorage->setToken(null);

            return $event->setResponse($response);

        }

    }
}