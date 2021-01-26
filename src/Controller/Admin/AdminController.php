<?php


namespace App\Controller\Admin;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RestrictedController
 * @package App\Controller\Restricted
 * @Route("/admin")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/", name="app_admin", methods={"GET"})
     *
     */
    public function index()
    {
        return $this->redirectToRoute('admin_activity_index');
    }

}