<?php

namespace App\Controller\Admin;

use App\Entity\Transaction;
use App\Form\Transaction1Type;
use App\Form\TransactionType;
use App\Repository\TransactionRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/transaction")
 */
class TransactionController extends AbstractController
{
    /**
     * @Route("/", name="admin_transaction_index", methods={"GET"})
     */
    public function index(TransactionRepository $transactionRepository, UserRepository $userRepository, Request $request): Response
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
        $orderBy = ['createTimestamp','desc'];
        if(array_key_exists("order",$data)) {
            $order = $data["order"];
            if($order == "created_asc"){
                $orderBy= ['createTimestamp','asc'];
            } else if($order == "created_desc") {
                $orderBy= ['createTimestamp','desc'];
            }
        }

        $filters = [];
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

        $items = $transactionRepository->table($filters,$orderBy, $limit, $offset);
        $total = $transactionRepository->countTable($filters);
        $pages = ceil($total/$limit);
        return $this->render('admin/transaction/index.html.twig', [
            'items' => $items,
            'pages'=>$pages,
            'page'=>$page,
            'limit'=>$limit,
            'offset'=>$offset,
            'total'=>$total,
            'order' => $order,
            'search' => $search,
            'date_from' => $date_from,
            'date_to' => $date_to,
            'user'=>$user,
            'waitingUsers' => $waitingUsers
        ]);
    }

    /**
     * @Route("/new", name="admin_transaction_new", methods={"GET","POST"})
     */
    public function new(UserRepository $userRepository,Request $request): Response
    {
        $user = $this->getUser();
        $waitingUsers = $userRepository->count(["status"=>0]);

        $transaction = new Transaction();
        $form = $this->createForm(TransactionType::class, $transaction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            $transaction->setCreateTimestamp(new \DateTime());
            $transaction->setCreateUser($user);
            $entityManager->persist($transaction);
            $entityManager->flush();

            return $this->redirectToRoute('admin_transaction_index');
        }

        return $this->render('admin/transaction/new.html.twig', [
            'transaction' => $transaction,
            'form' => $form->createView(),
            'user'=> $user,
            'waitingUsers'=>$waitingUsers
        ]);
    }

    /**
     * @Route("/{id}", name="admin_transaction_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Transaction $transaction): Response
    {
        if ($this->isCsrfTokenValid('delete'.$transaction->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $activity = $transaction->getActivity();
            if(!is_null($activity)){
                /* Open activity */
                $activity->setAcceptedUser(null);
                $entityManager->persist($activity);
            }
            /* Remove transaction */
            $entityManager->remove($transaction);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_transaction_index');
    }
}
