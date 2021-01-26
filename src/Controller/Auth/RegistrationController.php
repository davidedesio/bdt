<?php

namespace App\Controller\Auth;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\BankRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(
        Request $request,
        BankRepository $bankRepository,
        TranslatorInterface $translator,
        MailerInterface $mailer,
        UserPasswordEncoderInterface $passwordEncoder
    ): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        $bank = $bankRepository->find(1);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $user->setStatus(0);
            $user->setDel(0);
            $user->setCreateTimestamp(new \Datetime());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email
            $email = (new TemplatedEmail())
                ->from(new Address($bank->getSenderEmail(), $bank->getSenderName()))
                ->to($user->getEmail())
                ->subject($translator->trans('REGISTERED_TITLE'))
                ->htmlTemplate('auth/registration/email.html.twig')
            ;

            $mailer->send($email);

            return $this->redirectToRoute('app_registered');
        }

        return $this->render('auth/registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/registered", name="app_registered")
     */
    public function registered(){
        return $this->render('auth/registration/registered.html.twig');
    }
}
