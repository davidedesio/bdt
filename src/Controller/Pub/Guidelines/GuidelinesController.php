<?php


namespace App\Controller\Pub\Guidelines;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class GuidelinesController extends AbstractController
{
    /**
     * @Route("/guidelines", name="guidelines", methods={"GET", "POST"})
     */
    public function index()
    {
        return $this->render('public/guidelines/index.html.twig');
    }
}