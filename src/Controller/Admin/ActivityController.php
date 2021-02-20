<?php

namespace App\Controller\Admin;

use App\Entity\Activity;
use App\Entity\ActivityComment;
use App\Form\ActivityForAdminType;
use App\Repository\ActivityCategoryRepository;
use App\Repository\ActivityRepository;
use App\Repository\ActivityTypeRepository;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_ADMIN")
 * @Route("/admin/activity")
 */
class ActivityController extends AbstractController
{
    /**
     * @Route("/", name="admin_activity_index", methods={"GET"})
     */
    public function index(
        ActivityRepository $activityRepository,
        UserRepository $userRepository,
        Request $request): Response
    {
        $user = $this->getUser();

        $waitingUsers = $userRepository->count(["status"=>0]);

        $limit = 10;
        $data = $request->query->all();
        $page = 0;
        if(array_key_exists("page",$data)){
            $page = $data["page"];
            $offset = (int)$page*$limit;
        } else {
            $offset = 0;
        }

        $order = "data_asc";
        $orderBy = ['date','asc'];
        if(array_key_exists("order",$data)) {
            $order = $data["order"];
            if($order == "date_asc"){
                $orderBy= ['date','asc'];
            } else if($order == "date_desc"){
                $orderBy= ['date','desc'];
            } else if($order == "created_asc"){
                $orderBy= ['createTimestamp','asc'];
            } else if($order == "created_desc") {
                $orderBy= ['createTimestamp','desc'];
            }
        }

        $accepted = "all";
        $filters = [];

        if(array_key_exists("accepted",$data)) {
            $accepted = $data["accepted"];
            if($accepted == "yes"){
                $filters['acceptedUser'] = "IS NOT NULL";
            } else if($accepted == "no"){
                $filters['acceptedUser'] = "IS NULL";
            }
        }

        $search = "";
        if(array_key_exists("search",$data) && !empty($data["search"])) {
            $search = $data["search"];
            $filters["surname"] = $search;
        }

        $date_from = "";
        if(array_key_exists("date_from",$data) && !empty($data["date_from"])) {
            $date_from = $data["date_from"];
            $filters["date_from"] = $date_from;
        }

        $date_to = "";
        if(array_key_exists("date_to",$data) && !empty($data["date_to"])) {
            $date_to = $data["date_to"];
            $filters["date_to"] = $date_to;
        }

        $items = $activityRepository->table($filters,$orderBy, $limit, $offset);
        $total = $activityRepository->countTable($filters);
        $pages = ceil($total/$limit);
        return $this->render('admin/activity/index.html.twig', [
            'items' => $items,
            'pages'=>$pages,
            'page'=>$page,
            'limit'=>$limit,
            'offset'=>$offset,
            'total'=>$total,
            'order' => $order,
            'accepted' =>$accepted,
            'search' => $search,
            'date_from' => $date_from,
            'date_to' => $date_to,
            'user'=>$user,
            'waitingUsers'=>$waitingUsers
        ]);
    }

    /**
     * @Route("/new", name="admin_activity_new", methods={"GET","POST"})
     */
    public function new(UserRepository $userRepository, Request $request): Response
    {
        $user = $this->getUser();
        $waitingUsers = $userRepository->count(["status"=>0]);

        $activity = new Activity();
        $form = $this->createForm(ActivityForAdminType::class, $activity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $activity->setCreateTimestamp(new \Datetime());
            $entityManager->persist($activity);
            $entityManager->flush();

            return $this->redirectToRoute('admin_activity_index');
        }

        return $this->render('admin/activity/new.html.twig', [
            'activity' => $activity,
            'form' => $form->createView(),
            'user'=>$user,
            'waitingUsers'=>$waitingUsers
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin_activity_edit", methods={"GET","POST"})
     */
    public function edit(
        Request $request,
        Activity $activity,
        ActivityTypeRepository $activityTypeRepository,
        ActivityCategoryRepository $activityCategoryRepository,
        UserRepository $userRepository
    ): Response
    {
        $user = $this->getUser();
        $waitingUsers = $userRepository->count(["status"=>0]);

        $activityTypes = $activityTypeRepository->findAll();
        $activityCategories = $activityCategoryRepository->findAll();

        $comments = $activity->getActivityComments();
        $sinceCommentId = 0;
        if(count($comments)>0) {
            $sinceCommentId = $comments[count($comments) - 1]->getId();
        }

        $form = $this->createForm(ActivityForAdminType::class, $activity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_activity_index');
        }

        return $this->render('admin/activity/edit.html.twig', [
            'activity' => $activity,
            'form' => $form->createView(),
            'user'=>$user,
            'activityTypes'=>$activityTypes,
            'activityCategories'=>$activityCategories,
            "sinceCommentId" => $sinceCommentId,
            "waitingUsers" => $waitingUsers
        ]);
    }

    /**
     * @Route("/{id}", name="admin_activity_delete", methods={"DELETE"})
     */
    public function delete(
        Request $request,
        Activity $activity
    ): Response
    {
        if ($this->isCsrfTokenValid('delete'.$activity->getId(), $request->request->get('_token'))) {

            $entityManager = $this->getDoctrine()->getManager();

            // Delete comments
            foreach($activity->getActivityComments() as $activityComment){
                $entityManager->remove($activityComment);
            }

            // Delete matches
            foreach($activity->getActivityMatches() as $activityMatch){
                $entityManager->remove($activityMatch);
            }
            // Delete transactions
            foreach($activity->getTransactions() as $transaction){
                $entityManager->remove($transaction);
            }

            //Delete notifications
            foreach($activity->getNotifications() as $notification){
                $entityManager->remove($notification);
            }
            $entityManager->flush();

            $entityManager->remove($activity);
            $entityManager->flush();

        }


        return $this->redirectToRoute('admin_activity_index');
    }

    /**
     * @Route("/calendar/delete/{id}", name="admin_calendar_activity_delete", methods={"POST"})
     */
    public function calendarDelete(
        Request $request,
        Int $id,
        ActivityRepository $activityRepository
    ): Response
    {
        $activity = $activityRepository->find($id);
        $entityManager = $this->getDoctrine()->getManager();

        // Delete comments
        foreach($activity->getActivityComments() as $activityComment){
            $entityManager->remove($activityComment);
        }

        // Delete matches
        foreach($activity->getActivityMatches() as $activityMatch){
            $entityManager->remove($activityMatch);
        }
        // Delete transactions
        foreach($activity->getTransactions() as $transaction){
            $entityManager->remove($transaction);
        }

        //Delete notifications
        foreach($activity->getNotifications() as $notification){
            $entityManager->remove($notification);
        }
        $entityManager->flush();

        $entityManager->remove($activity);
        $entityManager->flush();




        return $this->json(['success'=>true]);
    }

    /**
     * @Route("/delete/comment/{id}", name="admin_activity_comment_delete", methods={"POST"})
     */
    public function deleteComment(Request $request, ActivityComment $activityComment): Response
    {

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($activityComment);
        $entityManager->flush();

        return $this->json(["done"]);
    }
}
