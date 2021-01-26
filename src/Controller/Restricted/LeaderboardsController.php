<?php


namespace App\Controller\Restricted;

use App\Helper\UserHelper;
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
        NotificationRepository $notificationRepository
    ){

        $user = $this->getUser();

        $statistics = $userHelper->computeStatitics($user);

        //Get latest notifications for this user
        $notifications = $notificationRepository->findBy(["user"=>$user,'dismissed'=>false],["id"=>"desc"]);

        $leaderboardPointsUsers = $userRepository->getLeaderboardPoints();
        $leaderboardDoneUsers = $userRepository->getLeaderboardDone();
        $leaderboardGetUsers = $userRepository->getLeaderboardGet();

        return $this->render('restricted/leaderboards/index.html.twig', [
            'user' => $user,
            'notifications' => $notifications,
            'statistics' => $statistics,
            'leaderboardPointsUsers'=>$leaderboardPointsUsers,
            'leaderboardDoneUsers'=>$leaderboardDoneUsers,
            'leaderboardGetUsers'=>$leaderboardGetUsers
        ]);
    }

}