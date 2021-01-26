<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserForAdminEditType;
use App\Form\UserForAdminInsertType;
use App\Helper\UserHelper;
use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @IsGranted("ROLE_ADMIN")
 * @Route("/admin/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="admin_user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository, UserHelper $userHelper, Request $request): Response
    {
        $user = $this->getUser();

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
        $orderBy = ['createTimestamp'=>'desc'];
        if(array_key_exists("order",$data)) {
            $order = $data["order"];
            if($order == "createTimestamp_desc"){
                $orderBy= ['createTimestamp'=>'desc'];
            } else if($order == "surname_asc"){
                $orderBy= ['surname'=>'asc'];
            } else if($order == "surname_desc"){
                $orderBy= ['surname'=>'desc'];
            } else if($order == "fiscalCode_asc") {
                $orderBy= ['fiscalCode'=>'asc'];
            } else if($order == "fiscalCode_desc") {
                $orderBy = ['fiscalCode'=>'desc'];
            }
        }

        $search = "";
        $filters = ["_del"=>0];
        if(array_key_exists("search",$data) && !empty($data["search"])) {
            $search = $data["search"];
            $filters = ["surname"=>$search,"_del"=>0];
        }

        $items = $userRepository->findBy($filters, $orderBy, $limit, $offset);

        $statistics = [];
        foreach($items as $index=>$item){
            $statistics[$item->getId()] = $userHelper->computeStatitics($item);
        }

        $total = $userRepository->count($filters);
        $pages = ceil($total/$limit);
        return $this->render('admin/user/index.html.twig', [
            'items' => $items,
            'statistics' => $statistics,
            'pages'=>$pages,
            'page'=>$page,
            'limit'=>$limit,
            'offset'=>$offset,
            'total'=>$total,
            'order' => $order,
            'search' =>$search,
            'user'=>$user
        ]);
    }

    /**
     * @Route("/new", name="admin_user_new", methods={"GET","POST"})
     */
    public function new(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = $this->getUser();
        $newUser = new User();
        $form = $this->createForm(UserForAdminInsertType::class, $newUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            // encode the plain password
            $newUser->setPassword(
                $passwordEncoder->encodePassword(
                    $newUser,
                    $form->get('plainPassword')->getData()
                )
            );

            $newUser->setStatus(1); //set active
            $newUser->setDel(0);
            $newUser->setCreateTimestamp(new \Datetime());
            $entityManager->persist($newUser);
            $entityManager->flush();

            return $this->redirectToRoute('admin_user_index');
        }

        return $this->render('admin/user/new.html.twig', [
            'user' => $user,
            'newUser'=>$newUser,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin_user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $editUser, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserForAdminEditType::class, $editUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();

            if(
                !is_null($form->get('plainPassword')->getData())
                && !empty($form->get('plainPassword')->getData())
            ){
                // encode the plain password
                $editUser->setPassword(
                    $passwordEncoder->encodePassword(
                        $editUser,
                        $form->get('plainPassword')->getData()
                    )
                );
            }

            $entityManager->persist($editUser);
            $entityManager->flush();

            return $this->redirectToRoute('admin_user_index');
        }

        return $this->render('admin/user/edit.html.twig', [
            'user' => $user,
            'editUser'=> $editUser,
            'form' => $form->createView(),
            'edit' => true
        ]);
    }

    /**
     * @Route("/{id}/change", name="admin_user_change_role", methods={"POST"})
     */
    public function changeRole(Request $request, User $changeUser): Response
    {
        if ($this->isCsrfTokenValid('change' . $changeUser->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            if(in_array("ROLE_ADMIN",$changeUser->getRoles())){
                $changeUser->setRoles(['ROLE_USER']);
            } else {
                $changeUser->setRoles(['ROLE_ADMIN']);
            }
            $entityManager->persist($changeUser);
            $entityManager->flush();
        }
        return $this->redirectToRoute('admin_user_index');
    }

    /**
     * @Route("/{id}/enable", name="admin_user_enable", methods={"POST"})
     */
    public function enable(Request $request, User $enableUser): Response
    {
        if ($this->isCsrfTokenValid('enable' . $enableUser->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $enableUser->setStatus(1);
            $entityManager->persist($enableUser);
            $entityManager->flush();
        }
        return $this->redirectToRoute('admin_user_index');
    }

    /**
     * @Route("/{id}/disable", name="admin_user_disable", methods={"POST"})
     */
    public function disable(Request $request, User $disableUser): Response
    {
        if ($this->isCsrfTokenValid('disable' . $disableUser->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $disableUser->setStatus(2);
            $entityManager->persist($disableUser);
            $entityManager->flush();
        }
        return $this->redirectToRoute('admin_user_index');
    }

    /**
     * @Route("/{id}/delete", name="admin_user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $deleteUser): Response
    {
        if ($this->isCsrfTokenValid('delete'.$deleteUser->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();

            $uniqueId = uniqid(); //anonimize user
            $deleteUser->setEmail("user".$uniqueId."@bdtpavia.com");
            $deleteUser->setName("user".$uniqueId);
            $deleteUser->setSurname("user".$uniqueId);
            $deleteUser->setFiscalCode("user".$uniqueId);
            $deleteUser->setAddress(null);
            $deleteUser->setCity(null);
            $deleteUser->setCountry(null);
            $deleteUser->setZipCode(null);
            $deleteUser->setDescription(null);
            $deleteUser->setPhone(null);
            $deleteUser->setPicture(null);
            $deleteUser->setBirthDate(null);
            $deleteUser->setDel(1); //logic delete
            $entityManager->persist($deleteUser);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_user_index');
    }
}
