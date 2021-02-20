<?php
namespace App\EventListener;

use App\Repository\ActivityRepository;
use App\Repository\BankRepository;
use App\Repository\UserRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\RouterInterface;

class NewActivityListener
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * @var BankRepository
     */
    private $bankRepository;

    /**
     * @var ActivityRepository
     */
    private $activityRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(
        RouterInterface $router,
        LoggerInterface $logger,
        MailerInterface $mailer,
        BankRepository $bankRepository,
        ActivityRepository $activityRepository,
        UserRepository $userRepository
    )
    {
        $this->router = $router;
        $this->logger = $logger;
        $this->mailer = $mailer;
        $this->bankRepository = $bankRepository;
        $this->activityRepository = $activityRepository;
        $this->userRepository = $userRepository;
    }


    public function onKernelTerminate(TerminateEvent $event)
    {
        // Get route
        $currentRoute = $this->router->match($event->getRequest()->getPathInfo());
        if ('activity_new_post' === $currentRoute['_route']) {
            // If this is activity_new_post route
            $this->logger->info("Starting sending email to for this new activity to users...");

            $activityId = $event->getRequest()->attributes->get('activityId');
            $this->logger->info($activityId);

            $activity = $this->activityRepository->find($activityId);

            $users = $this->userRepository->findBy(["_del"=>0,"activitiesEmail"=>1]); //only active users which want to get emails
            foreach($users as $user){

                if($user!=$activity->getCreateUser()){ //only if not create user
                    $userEmail = $user->getEmail();
                    $this->logger->info("Sending email to ".$userEmail);

                    $bank = $this->bankRepository->find(1);

                    $newTitle = $activity->getType()->getNameIT()." ".$activity->getCategory()->getNameIT(). " di ".$activity->getCreateUser()->getName();
                    $email = (new TemplatedEmail())
                        ->from(new Address($bank->getSenderEmail(), $bank->getSenderName()))
                        ->to($userEmail)
                        ->subject($newTitle)
                        ->htmlTemplate('restricted/activity/partials/newEmail.html.twig')
                        ->context([
                            'newTitle' => $newTitle,
                            'activity' => $activity,
                            'website'=> $_ENV['WEBSITE']
                        ]);

                    $this->mailer->send($email);
                }

            }

            $this->logger->info("Sending email for new activity completed!");
        }
    }
}

