<?php


namespace App\Controller\Restricted;

use App\Entity\Activity;
use App\Entity\ActivityComment;
use App\Entity\ActivityMatch;
use App\Entity\Notification;
use App\Entity\Transaction;
use App\Form\ActivityType;
use App\Helper\UserHelper;
use App\Repository\ActivityCategoryRepository;
use App\Repository\ActivityCommentRepository;
use App\Repository\ActivityMatchRepository;
use App\Repository\ActivityRepository;
use App\Repository\ActivityTypeRepository;
use App\Repository\BankRepository;
use App\Repository\NotificationRepository;
use App\Repository\UserRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ActivityController extends AbstractController
{
    /**
     * @Route("/restricted/activity", name="app_activity", methods={"GET"})
     *
     */
    public function activity(
        UserHelper $userHelper,
        ActivityRepository $activityRepository,
        ActivityTypeRepository $activityTypeRepository,
        ActivityCategoryRepository $activityCategoryRepository,
        NotificationRepository $notificationRepository,
        Request $request
    ){

        //Get logged user
        $user = $this->getUser();

        //Get request data
        $data = $request->query->all();

        //Type filter
        $activityTypeFilterId = null;
        $activityTypeFilter = null;
        if(
            isset($data["type"]) &&
            (isset($data["type"])=="1" || isset($data["type"])=="2")
        ){
            $activityTypeFilterId = (int) $data["type"];
            $activityTypeFilter = $activityTypeRepository->find($activityTypeFilterId);
        }

        //Category filter
        $activityCategoryFilterId = null;
        $activityCategoryFilter = null;
        if(
            isset($data["category"])
        ){
            $activityCategoryFilterId = (int) $data["category"];
            $activityCategoryFilter = $activityCategoryRepository->find($activityCategoryFilterId);
        }

        //User filter
        $userFilter = null;
        if(
            isset($data["user"]) &&
            (isset($data["user"])=="1" || isset($data["user"])=="2" || isset($data["user"])=="3")
        ){
            $userFilter = (int) $data["user"];
        }

        // Get acivities for listing
        $filters = [
            "type"=>$activityTypeFilter,
            "category"=>$activityCategoryFilter,
            "user"=>$userFilter
        ];

        // Pagination
        $page = 0;
        $limit = 10;
        if(array_key_exists("page",$data)){
            $page = $data["page"];
            $offset = (int)$page*$limit;
        } else {
            $offset = 0;
        }

        $activities = $activityRepository->search($filters,$user,$offset,$limit);
        $acitivitiesUsersStatistics = [];
        foreach($activities as $activity){
            $acitivitiesUsersStatistics[] = $userHelper->computeStatitics($activity->getCreateUser());
        }
        $total = $activityRepository->countSearch($filters,$user);
        $pages = ceil($total/$limit);
        $activityTypes = $activityTypeRepository->findAll();
        $activityCategories = $activityCategoryRepository->findAll();

        // Compute user statistics
        $statistics = $userHelper->computeStatitics($user);

        // Get nextActivity
        $nextActivity = $activityRepository->next($user);

        //Get latest notifications for this user
        $notifications = $notificationRepository->findBy(["user"=>$user,'dismissed'=>false],["id"=>"desc"]);

        $activity = new Activity();
        $form = $this->createForm(ActivityType::class, $activity);

        return $this->render('restricted/activity/index.html.twig', [
            'user' => $user,
            'notifications'=>$notifications,
            'activities'=>$activities,
            'acitivitiesUsersStatistics'=>$acitivitiesUsersStatistics,
            'nextActivity'=>$nextActivity,
            'activityTypes'=>$activityTypes,
            'activityCategories'=>$activityCategories,
            'statistics' => $statistics,
            'activityTypeFilter' => $activityTypeFilterId,
            'activityCategoryFilter' => $activityCategoryFilterId,
            'userFilter'=> $userFilter,
            'pages'=>$pages,
            'page'=>$page,
            'limit'=>$limit,
            'offset'=>$offset,
            'total'=>$total,
            'form'=> $form->createView()
        ]);
    }

    /**
     * @Route("/restricted/timeline", name="app_timeline", methods={"GET"})
     *
     */
    public function timeline(
        UserHelper $userHelper,
        ActivityRepository $activityRepository,
        NotificationRepository $notificationRepository,
        Request $request
    ){

        //Get logged user
        $user = $this->getUser();

        //Get request data
        $data = $request->query->all();

        // Compute user statistics
        $statistics = $userHelper->computeStatitics($user);

        // Get acivities for listing
        // Pagination
        $page = 0;
        $limit = 10;
        if(array_key_exists("page",$data)){
            $page = $data["page"];
            $offset = (int)$page*$limit;
        } else {
            $offset = 0;
        }

        $activities = $activityRepository->findForTimeline($user,$offset,$limit);
        $acitivitiesUsersStatistics = [];
        foreach($activities as $activity){
            $acitivitiesUsersStatistics[] = $userHelper->computeStatitics($activity->getCreateUser());
        }
        $total = $activityRepository->countForTimeline($user);
        $pages = ceil($total/$limit);

        // Get nextActivity
        $nextActivity = $activityRepository->next($user);

        //Get latest notifications for this user
        $notifications = $notificationRepository->findBy(["user"=>$user,'dismissed'=>false],["id"=>"desc"]);

        return $this->render('restricted/activity/timeline.html.twig', [
            'user' => $user,
            'notifications'=>$notifications,
            'activities'=>$activities,
            'acitivitiesUsersStatistics'=>$acitivitiesUsersStatistics,
            'nextActivity'=>$nextActivity,
            'statistics' => $statistics,
            'pages'=>$pages,
            'page'=>$page,
            'limit'=>$limit,
            'offset'=>$offset,
            'total'=>$total
        ]);
    }

    /**
     * @Route("/restricted/calendar", name="app_calendar", methods={"GET"})
     *
     */
    public function calendar(
        UserHelper $userHelper,
        ActivityRepository $activityRepository,
        NotificationRepository $notificationRepository,
        Request $request
    ){

        //Get logged user
        $user = $this->getUser();

        //Get request data
        $data = $request->query->all();

        // Compute user statistics
        $statistics = $userHelper->computeStatitics($user);

        // Get nextActivity
        $nextActivity = $activityRepository->next($user);

        //Get latest notifications for this user
        $notifications = $notificationRepository->findBy(["user"=>$user,'dismissed'=>false],["id"=>"desc"]);

        return $this->render('restricted/activity/calendar.html.twig', [
            'user' => $user,
            'notifications'=>$notifications,
            'nextActivity'=>$nextActivity,
            'statistics' => $statistics,
        ]);
    }

    /**
     * @Route("/restricted/activity", name="activity_new", methods={"POST"})
     */
    public function new(Request $request, TranslatorInterface $translator): Response
    {
        $user = $this->getUser();
        $activity = new Activity();
        $form = $this->createForm(ActivityType::class, $activity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $activity->setCreateTimestamp(new \DateTime());
            $activity->setCreateUser($user);

            //Create notification
            $notification = new Notification();
            $notification->setUser($user);
            $notification->setCreateUser($user);
            $notification->setCreateTimestamp(new \DateTime());
            $notification->setMessage($translator->trans('ACTIVITY_INSERT_NOTIFICATION'));
            $notification->setActivity($activity);
            $notification->setDismissed(0);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($activity);
            $entityManager->persist($notification);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_activity');
    }

    /**
     * @Route("/restricted/activity/match/{id}", name="activity_match_new", methods={"POST"})
     */
    public function match(
        Activity $activity,
        BankRepository $bankRepository,
        ActivityMatchRepository $activityMatchRepository,
        TranslatorInterface $translator,
        MailerInterface $mailer
    ): Response
    {
        $user = $this->getUser();

        //Only if this activity is not created by logged user
        //and if there is not an activityMatch already existing for this activity created by logged user
        if(
            $user!=$activity->getCreateUser()
            && is_null($activityMatchRepository->findOneBy(["activity"=>$activity,"createUser"=>$user]))
        ){
            $activityMatch = new ActivityMatch();
            $activityMatch->setActivity($activity);
            $activityMatch->setCreateUser($user);
            $activityMatch->setCreateTimestamp(new \DateTime());

            $activityTypeId = $activity->getType()->getId();

            //Create notifications

            //Notification for logged user
            $notification1 = new Notification();
            $notification1->setUser($user);
            $notification1->setCreateUser($user);
            $notification1->setCreateTimestamp(new \DateTime());
            $notification1->setActivity($activity);
            $notification1->setDismissed(0);

            //Notification for activity createUser (can't be logged user)
            $notification2 = new Notification();
            $notification2->setUser($activity->getCreateUser());
            $notification2->setCreateUser($user);
            $notification2->setCreateTimestamp(new \DateTime());
            $notification2->setActivity($activity);
            $notification2->setDismissed(0);

            $matchTitle = "";
            if($activityTypeId==1){
                //REQUEST
                $notification1->setMessage($translator->trans('ACTIVITY_MATCH_AVAILABLE_ACTION'));
                $matchTitle = $user->getName()." ".$user->getSurname()." ".$translator->trans('ACTIVITY_MATCH_AVAILABLE_POSTFIX');
                $notification2->setMessage($matchTitle);
            } else if($activityTypeId==2) {
                //OFFER
                $notification1->setMessage($translator->trans('ACTIVITY_MATCH_NEEDED_ACTION'));
                $matchTitle = $user->getName()." ".$user->getSurname()." ".$translator->trans('ACTIVITY_MATCH_NEEDED_POSTFIX');
                $notification2->setMessage($matchTitle);
            }

            //email for createUser

            $bank = $bankRepository->find(1);
            $email = (new TemplatedEmail())
                ->from(new Address($bank->getSenderEmail(), $bank->getSenderName()))
                ->to($activity->getCreateUser()->getEmail())
                ->subject($matchTitle)
                ->htmlTemplate('restricted/activity/partials/matchEmail.html.twig')
                ->context([
                    'matchTitle' => $matchTitle,
                    'activity' => $activity,
                    'website'=> $_ENV['WEBSITE']
                ])
            ;

            $mailer->send($email);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($notification1);
            $entityManager->persist($notification2);
            $entityManager->persist($activityMatch);
            $entityManager->flush();

            return $this->json(["error"=>false,"id"=>$activity->getId(),"type"=>$activityTypeId]);
        }

        return $this->json(["error"=>true]);
    }

    /**
     * @Route("/restricted/activity/accept/{id}", name="activity_accept_new", methods={"POST"})
     */
    public function accept(
        Activity $activity,
        Request $request,
        BankRepository $bankRepository,
        UserRepository $userRepository,
        TranslatorInterface $translator,
        MailerInterface $mailer
    ): Response
    {
        $user = $this->getUser();
        $entityManager = $this->getDoctrine()->getManager();

        //Get passed id
        $acceptedUserId = (int)$request->request->get("user_id");

        //Only if this activity is created by logged user
        //and if there is not another accepted user
        if(
            $user==$activity->getCreateUser()
            && is_null($activity->getAcceptedUser())
        ){
            $acceptedUser = $userRepository->find($acceptedUserId);
            $activity->setAcceptedUser($acceptedUser);

            $activityTypeId = $activity->getType()->getId();
            $transaction = new Transaction();
            $transaction->setActivity($activity);
            $transaction->setCreateTimestamp(new \DateTime());
            $transaction->setCreateUser($user);
            $transaction->setValue($activity->getEstimatedValue());
            if($activityTypeId==1){
                //REQUEST -> Who choose (logged $user) should send trasaction to choosen one ($acceptedUser)
                $transaction->setUserFrom($user);
                $transaction->setUserTo($acceptedUser);
            } else if($activityTypeId==2){
                //OFFER -> Who choose (logged $user) should get trasaction from choosen one ($acceptedUser)
                $transaction->setUserFrom($acceptedUser);
                $transaction->setUserTo($user);
            }

            $entityManager->persist($transaction);
            $entityManager->persist($activity);

            //Notification for logged user
            $notification1 = new Notification();
            $chooseTitle = "";
            if($activityTypeId==1){
                //REQUEST
                $chooseTitle = $translator->trans('ACTIVITY_ACCEPT_SEND_ACTION')." ".$acceptedUser->getName()." ".$acceptedUser->getSurname();
                $notification1->setMessage($chooseTitle);
            } else if($activityTypeId==2){
                //OFFER
                $chooseTitle = $translator->trans('ACTIVITY_ACCEPT_GET_ACTION')." ".$acceptedUser->getName()." ".$acceptedUser->getSurname();
                $notification1->setMessage($chooseTitle);
            }

            $notification1->setUser($user);
            $notification1->setCreateUser($user);
            $notification1->setCreateTimestamp(new \DateTime());
            $notification1->setActivity($activity);
            $notification1->setDismissed(0);
            $entityManager->persist($notification1);

            //email for logged user
            $bank = $bankRepository->find(1);
            $chooseEmail = (new TemplatedEmail())
                ->from(new Address($bank->getSenderEmail(), $bank->getSenderName()))
                ->to($user->getEmail())
                ->subject($chooseTitle)
                ->htmlTemplate('restricted/activity/partials/chooseEmail.html.twig')
                ->context([
                    'chooseTitle' => $chooseTitle,
                    'acceptedUser' => $acceptedUser,
                    'activity' => $activity,
                    'website'=> $_ENV['WEBSITE']
                ])
            ;

            $mailer->send($chooseEmail);

            //Notification for acceptedUser
            $notification2 = new Notification();
            $choosenTitle = "";
            if($activityTypeId==1){
                //REQUEST -> Who choose (logged $user) should send trasaction to choosen one ($acceptedUser)
                $choosenTitle = $user->getName()." ".$user->getSurname()." ".$translator->trans('ACTIVITY_ACCEPT_SEND_POSTFIX');
                $notification2->setMessage($choosenTitle);
            } else if($activityTypeId==2){
                //OFFER -> Who choose (logged $user) should get trasaction from choosen one ($acceptedUser)
                $choosenTitle = $user->getName()." ".$user->getSurname()." ".$translator->trans('ACTIVITY_ACCEPT_GET_POSTFIX');
                $notification2->setMessage($choosenTitle);
            }
            $notification2->setUser($acceptedUser);
            $notification2->setCreateUser($user);
            $notification2->setCreateTimestamp(new \DateTime());
            $notification2->setActivity($activity);
            $notification2->setDismissed(0);
            $entityManager->persist($notification2);

            //email for accepted user
            $bank = $bankRepository->find(1);
            $choosenEmail = (new TemplatedEmail())
                ->from(new Address($bank->getSenderEmail(), $bank->getSenderName()))
                ->to($acceptedUser->getEmail())
                ->subject($choosenTitle)
                ->htmlTemplate('restricted/activity/partials/choosenEmail.html.twig')
                ->context([
                    'choosenTitle' => $choosenTitle,
                    'user' => $user,
                    'activity' => $activity,
                    'website'=> $_ENV['WEBSITE']
                ])
            ;

            $mailer->send($choosenEmail);

            foreach($activity->getActivityMatches() as $activityMatch){
                //notification for each user which has matched this activity if different from acceptedUser
                $activityMatchCreateUser = $activityMatch->getCreateUser();
                if($acceptedUser!=$activityMatchCreateUser){
                    $notification3 = new Notification();
                    if($activityTypeId==1){
                        //REQUEST -> Who choose (logged $user) should send trasaction to choosen one ($acceptedUser)
                        $notification3->setMessage($user->getName()." ".$user->getSurname()." ".$translator->trans('ACTIVITY_ACCEPT_SEND_NEGATIVE_POSTFIX'));
                    } else if($activityTypeId==2){
                        //OFFER -> Who choose (logged $user) should get trasaction from choosen one ($acceptedUser)
                        $notification3->setMessage($user->getName()." ".$user->getSurname()." ".$translator->trans('ACTIVITY_ACCEPT_GET_NEGATIVE_POSTFIX'));
                    }
                    $notification3->setUser($activityMatchCreateUser);
                    $notification3->setCreateUser($user);
                    $notification3->setCreateTimestamp(new \DateTime());
                    $notification3->setActivity($activity);
                    $notification3->setDismissed(0);
                    $entityManager->persist($notification3);
                }
            }

            $entityManager->flush();

            return $this->json(["error"=>false,"id"=>$activity->getId(),"type"=>$activity->getType()->getId()]);
        }

        return $this->json(["error"=>true]);
    }

    /**
     * @Route("/restricted/activity/comment/{id}", name="activity_comment_new", methods={"POST"})
     */
    public function comment(
        Activity $activity,
        Request $request,
        BankRepository $bankRepository,
        TranslatorInterface $translator,
        MailerInterface $mailer
    ): Response
    {
        $user = $this->getUser();
        $entityManager = $this->getDoctrine()->getManager();

        $comment = $request->request->get("comment");

        if(!is_null($comment)){

            $activityComment = new ActivityComment();
            $activityComment->setComment($comment);
            $activityComment->setActivity($activity);
            $activityComment->setCreateUser($user);
            $activityComment->setCreateTimestamp(new \DateTime());

            //Notification for logged user
            $notification1 = new Notification();
            $notification1->setMessage($translator->trans('ACTIVITY_COMMENT_ACTION'));
            $notification1->setUser($user);
            $notification1->setCreateUser($user);
            $notification1->setCreateTimestamp(new \DateTime());
            $notification1->setActivity($activity);
            $notification1->setDismissed(0);
            $entityManager->persist($notification1);

            $bank = $bankRepository->find(1);

            $activityCreateUser = $activity->getCreateUser();
            if($user!=$activityCreateUser){
                //Notification for activity createUser if different from logged
                $notification2 = new Notification();
                $notification2->setMessage($user->getName()." ".$user->getSurname()." ".$translator->trans('ACTIVITY_COMMENT_POSTFIX'));
                $notification2->setUser($activityCreateUser);
                $notification2->setCreateUser($user);
                $notification2->setCreateTimestamp(new \DateTime());
                $notification2->setActivity($activity);
                $notification2->setDismissed(0);
                $entityManager->persist($notification2);

                $email = (new TemplatedEmail())
                    ->from(new Address($bank->getSenderEmail(), $bank->getSenderName()))
                    ->to($activityCreateUser->getEmail())
                    ->subject($user->getName()." ".$user->getSurname()." ".$translator->trans('ACTIVITY_COMMENT_POSTFIX'))
                    ->htmlTemplate('restricted/activity/partials/commentEmail.html.twig')
                    ->context([
                        'matchTitle' => $user->getName()." ".$user->getSurname()." ".$translator->trans('ACTIVITY_COMMENT_POSTFIX'),
                        'activity' => $activity,
                        'website'=> $_ENV['WEBSITE']
                    ])
                ;

                $mailer->send($email);
            }

            foreach($activity->getActivityMatches() as $activityMatch){
                //notification for each user which has matched this activity if different from logged
                $activityMatchCreateUser = $activityMatch->getCreateUser();
                if($user!=$activityMatchCreateUser){
                    $notification3 = new Notification();
                    $notification3->setMessage($user->getName()." ".$user->getSurname()." ".$translator->trans('ACTIVITY_COMMENT_POSTFIX'));
                    $notification3->setUser($activityMatchCreateUser);
                    $notification3->setCreateUser($user);
                    $notification3->setCreateTimestamp(new \DateTime());
                    $notification3->setActivity($activity);
                    $notification3->setDismissed(0);
                    $entityManager->persist($notification3);

                    $email = (new TemplatedEmail())
                        ->from(new Address($bank->getSenderEmail(), $bank->getSenderName()))
                        ->to($activityMatchCreateUser->getEmail())
                        ->subject($user->getName()." ".$user->getSurname()." ".$translator->trans('ACTIVITY_COMMENT_POSTFIX'))
                        ->htmlTemplate('restricted/activity/partials/commentEmail.html.twig')
                        ->context([
                            'matchTitle' => $user->getName()." ".$user->getSurname()." ".$translator->trans('ACTIVITY_COMMENT_POSTFIX'),
                            'activity' => $activity,
                            'website'=> $_ENV['WEBSITE']
                        ])
                    ;

                    $mailer->send($email);
                }
            }

            $entityManager->persist($activityComment);
            $entityManager->flush();

            return $this->render('restricted/activity/partials/comment.html.twig',[
                'activity'=>$activity,
                'comment'=>$activityComment,
                'user'=>$user
            ]);

        }

        return new Response();
    }

    /**
     * @Route("/restricted/activity/comments/{id}", name="activity_comments_fetch", methods={"POST"})
     */
    public function fetchComments(
        Activity $activity,
        Request $request,
        ActivityCommentRepository $activityCommentRepository
    ): Response
    {
        $user = $this->getUser();

        $sinceCommentId = $request->request->get("since");

        /* Fetch other comments */
        [$comments,$sinceCommentId] = $activityCommentRepository->fetchSince($activity,$sinceCommentId,$user);

        $html= $this->renderView('restricted/activity/partials/listing-fetch-comments.html.twig',[
            'activity'=>$activity,
            'comments'=>$comments,
            'user'=>$user
        ]);

        return $this->json([
            "html"=>$html,
            "since"=>$sinceCommentId
        ]);
    }

    /**
     * @Route("/restricted/notifications/dismissall", name="app_activity_notifications_dismiss_all", methods={"GET"})
     *
     */
    public function notificationsDismissAll(
        NotificationRepository $notificationRepository
    ){
        $user = $this->getUser();
        $notifications = $notificationRepository->findBy(["user"=>$user]);
        //only if this notification is for logged user
        foreach($notifications as $notification){
            $notification->setDismissed(true); //dismiss notification
            $this->getDoctrine()->getManager()->persist($notification);
        }

        $this->getDoctrine()->getManager()->flush();
        return $this->json(["error"=>false]);
    }

    /**
     * @Route("/restricted/activity/{id}", name="app_activity_detail", methods={"GET"})
     *
     */
    public function activityDetail(
        Activity $activity,
        UserHelper $userHelper,
        ActivityRepository $activityRepository,
        ActivityTypeRepository $activityTypeRepository,
        ActivityCategoryRepository $activityCategoryRepository,
        NotificationRepository $notificationRepository,
        Request $request
    ){

        //Get logged user
        $user = $this->getUser();
        //Get request data
        $data = $request->query->all();
        if(array_key_exists("notification",$data)){
            $notificationId = (int) $data["notification"];
            $notification = $notificationRepository->find($notificationId);
            //only if this notification is for logged user
            if($notification->getUser()==$user){
                $notification->setDismissed(true); //dismiss notification
                $this->getDoctrine()->getManager()->persist($notification);
                $this->getDoctrine()->getManager()->flush();
            }
        }

        // Compute user statistics
        $statistics = $userHelper->computeStatitics($user);

        $activityTypes = $activityTypeRepository->findAll();
        $activityCategories = $activityCategoryRepository->findAll();

        $comments = $activity->getActivityComments();
        $sinceCommentId = 0;
        if(count($comments)>0) {
            $sinceCommentId = $comments[count($comments) - 1]->getId();
        }

        // Get nextActivity
        $nextActivity = $activityRepository->next($user);

        //Get latest notifications for this user
        $notifications = $notificationRepository->findBy(["user"=>$user,'dismissed'=>false],["id"=>"desc"]);

        return $this->render('restricted/activity/show.html.twig', [
            'user' => $user,
            'notifications' => $notifications,
            'activity' => $activity,
            'nextActivity' => $nextActivity,
            'activityTypes'=>$activityTypes,
            'activityCategories'=>$activityCategories,
            'statistics' => $statistics,
            "sinceCommentId" => $sinceCommentId
        ]);
    }

}