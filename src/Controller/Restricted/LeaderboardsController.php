<?php


namespace App\Controller\Restricted;

use App\Helper\UserHelper;
use App\Repository\ActivityRepository;
use App\Repository\NotificationRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class LeaderboardsController extends AbstractController
{
    /**
     * @Route("/restricted/leaderboards", name="app_leaderboards")
     *
     */
    public function activity(
        UserHelper $userHelper,
        UserRepository $userRepository,
        ActivityRepository $activityRepository,
        NotificationRepository $notificationRepository
    ){

        $user = $this->getUser();

        $statistics = $userHelper->computeStatitics($user);

        //Get latest notifications for this user
        $notifications = $notificationRepository->findBy(["user"=>$user,'dismissed'=>false],["id"=>"desc"]);

        $leaderboardPointsUsers = $userRepository->getLeaderboardPoints();
        $leaderboardDoneUsers = $userRepository->getLeaderboardDone();
        $leaderboardGetUsers = $userRepository->getLeaderboardGet();
        $leaderboardMedalUsers = $userRepository->getLeaderboardMedal();

        /*Calc month*/
        $month = (int) date('m');
        $year = (int) date('Y');
        // Create two times at the start of this month and next month
        $startDate = new \DateTime("$year-$month-01T00:00:00");
        $startDate2 = new \DateTime("$year-$month-01T00:00:00");
        $endDate = $startDate2->modify('last day of this month')->setTime(23, 59, 59);

        $startDate = $startDate->format("‌​Y-m-d");
        $startDate = strlen($startDate)>10?substr($startDate, strlen($startDate)-10):$startDate;
        $endDate = $endDate->format("‌​Y-m-d");
        $endDate = strlen($endDate)>10?substr($endDate, strlen($endDate)-10):$endDate;

        $totalActivities = $activityRepository->countTotal($startDate,$endDate);
        $closedActivities = $activityRepository->countClosed($startDate,$endDate);
        $totalActivitiesHours = $activityRepository->countTotalHours($startDate,$endDate);
        $closedActivitiesHours = $activityRepository->countClosedHours($startDate,$endDate);

        return $this->render('restricted/leaderboards/index.html.twig', [
            'user' => $user,
            'notifications' => $notifications,
            'statistics' => $statistics,
            'leaderboardPointsUsers'=>$leaderboardPointsUsers,
            'leaderboardDoneUsers'=>$leaderboardDoneUsers,
            'leaderboardGetUsers'=>$leaderboardGetUsers,
            'leaderboardMedalUsers'=>$leaderboardMedalUsers,
            'totalActivities' => $totalActivities,
            'closedActivities' => $closedActivities,
            'totalActivitiesHours' => $totalActivitiesHours,
            'closedActivitiesHours' => $closedActivitiesHours
        ]);
    }

}