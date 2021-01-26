<?php


namespace App\Controller\Admin;


use App\Repository\ActivityCategoryRepository;
use App\Repository\ActivityTypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RestrictedController
 * @package App\Controller\Restricted
 * @Route("/admin")
 */
class CalendarController extends AbstractController
{

    /**
     * @Route("/calendar", name="admin_calendar", methods={"GET"})
     *
     */
    public function calendar(
        ActivityTypeRepository $activityTypeRepository,
        ActivityCategoryRepository $activityCategoryRepository
    ){

        //Get logged user
        $user = $this->getUser();
        $activityTypes = $activityTypeRepository->findAll();
        $activityCategories = $activityCategoryRepository->findAll();

        return $this->render('admin/activity/calendar.html.twig', [
            'user' => $user,
            'activityTypes'=>$activityTypes,
            'activityCategories'=>$activityCategories,
        ]);
    }

}