<?php


namespace App\Controller\Restricted;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RestrictedController
 * @package App\Controller\Restricted
 * @Route("/restricted")
 */
class RestrictedController extends AbstractController
{
    /**
     * @Route("/", name="app_restricted", methods={"GET"})
     *
     */
    public function index()
    {
        return $this->redirectToRoute('app_activity');
    }

}