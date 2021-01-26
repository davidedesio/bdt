<?php


namespace App\Controller\Restricted;

use App\Helper\UserHelper;
use App\Repository\NotificationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CreditController extends AbstractController
{
    /**
     * @Route("/restricted/credit", name="app_credit")
     *
     */
    public function activity(UserHelper $userHelper, NotificationRepository $notificationRepository){

        $user = $this->getUser();

        [$sentTransactions,$receivedTransactions] = $userHelper->getTransactions($user);

        $statistics = $userHelper->computeStatitics($user);

        //Get latest notifications for this user
        $notifications = $notificationRepository->findBy(["user"=>$user,'dismissed'=>false],["id"=>"desc"]);

        return $this->render('restricted/credit/index.html.twig', [
            'user' => $user,
            'notifications' => $notifications,
            'sentTransactions'=>$sentTransactions,
            'receivedTransactions'=>$receivedTransactions,
            'statistics' => $statistics
        ]);
    }

}