<?php

namespace App\Controller\Auth;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LogoutController extends AbstractController
{
    /**
     * @Route("/logout", name="app_logout", methods={"GET"})
     */
    public function logout()
    {
        // controller can be blank: it will never be executed!
    }
}