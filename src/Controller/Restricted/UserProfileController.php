<?php


namespace App\Controller\Restricted;

use App\Entity\Notification;
use App\Entity\User;
use App\Form\UserType;
use App\Helper\UserHelper;
use App\Repository\BankRepository;
use App\Repository\NotificationRepository;
use App\Repository\SkillRepository;
use App\Repository\TagRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Translation\Translator;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserProfileController extends AbstractController
{
    private $tagRepository;
    private $skillRepository;

    /**
     * RestrictedController constructor.
     * @param $tagRepository
     * @param $skillRepository
     */
    public function __construct(TagRepository $tagRepository, SkillRepository $skillRepository)
    {
        $this->tagRepository = $tagRepository;
        $this->skillRepository = $skillRepository;
    }

    /**
     * @Route("/restricted/user-profile", name="app_user_profile", methods={"GET"})
     */
    public function profile(UserHelper $userHelper, NotificationRepository $notificationRepository)
    {
        $user = $this->getUser();
        $skills = $this->skillRepository->findAll();
        $tags = $this->tagRepository->findAll();
        $form = $this->createForm(UserType::class, $user);

        $statistics = $userHelper->computeStatitics($user);

        //Get latest notifications for this user
        $notifications = $notificationRepository->findBy(["user"=>$user,'dismissed'=>false],["id"=>"desc"]);

        return $this->render('restricted/user-profile/index.html.twig', [
            'user' => $user,
            'notifications'=>$notifications,
            'skills' => $skills,
            'tags' => $tags,
            'form' => $form->createView(),
            'statistics' => $statistics,
            'passwordChanged' => false
        ]);
    }

    /**
     * @Route("/restricted/user-profile", name="post_user_profile", methods={"POST"})
     * @param Request $request
     */
    public function post_profile(
        Request $request,
        BankRepository $bankRepository,
        TranslatorInterface $translator,
        UserPasswordEncoderInterface $passwordEncoder,
        UserHelper $userHelper,
        NotificationRepository $notificationRepository, MailerInterface $mailer
    )
    {
        $user = $this->getUser();
        $skills = $this->skillRepository->findAll();
        $tags = $this->tagRepository->findAll();
        $statistics = $userHelper->computeStatitics($user);

        //Get latest notifications for this user
        $notifications = $notificationRepository->findBy(["user"=>$user,'dismissed'=>false],["id"=>"desc"]);

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        $passwordChanged = false;
        if ($form->isSubmitted() && $form->isValid()) {

            if(
                !is_null($form->get('plainPassword')->getData())
                && !empty($form->get('plainPassword')->getData())
            ){
                // encode the plain password
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );

                $passwordChanged = true;

                $bank = $bankRepository->find(1);
                $email = (new TemplatedEmail())
                    ->from(new Address($bank->getSenderEmail(), $bank->getSenderName()))
                    ->to($user->getEmail())
                    ->subject($translator->trans('RESET_PASSWORD_SUCCESS_EMAIL_TITLE'))
                    ->htmlTemplate('restricted/user-profile/email.html.twig')
                    ->context([
                        'user' => $user,
                    ])
                ;

                $mailer->send($email);
            }

            $this->getDoctrine()->getManager()->persist($user);
            $this->getDoctrine()->getManager()->flush();
        }

        return $this->render('restricted/user-profile/index.html.twig', [
            'user' => $user,
            'notifications'=>$notifications,
            'skills' => $skills,
            'tags' => $tags,
            'form' => $form->createView(),
            'statistics' => $statistics,
            'passwordChanged' => $passwordChanged
        ]);
    }

    /**
     * @Route("/restricted/user-profile-delete", name="delete_user_profile", methods={"DELETE"})
     */
    public function delete(Request $request): Response
    {
        $deleteUser = $this->getUser();

        if ($this->isCsrfTokenValid('delete'.$deleteUser->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();

            $uniqueId = uniqid(); //anonimize user
            $deleteUser->setEmail("user" . $uniqueId . "@bdtpavia.com");
            $deleteUser->setName("user" . $uniqueId);
            $deleteUser->setSurname("user" . $uniqueId);
            $deleteUser->setFiscalCode("user" . $uniqueId);
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

            return $this->redirectToRoute('app_logout');
        }
    }

    /**
     * @Route("/restricted/member-profile/{id}", name="app_member_profile", methods={"GET"})
     */
    public function member(
        User $member,
        UserHelper $userHelper,
        NotificationRepository $notificationRepository)
    {
        $user = $this->getUser();

        if($member->getId()==$user->getId()){
            return $this->redirectToRoute('app_user_profile');
        }

        $statistics = $userHelper->computeStatitics($user);
        $memberStatistics = $userHelper->computeStatitics($member);

        //Get latest notifications for this user
        $notifications = $notificationRepository->findBy(["user"=>$user,'dismissed'=>false],["id"=>"desc"]);

        return $this->render('restricted/member-profile/index.html.twig', [
            'user' => $user,
            'statistics' => $statistics,
            'member' => $member,
            'memberStatistics' => $memberStatistics,
            'notifications'=>$notifications,
            'passwordChanged' => false
        ]);
    }

}