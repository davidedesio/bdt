<?php


namespace App\Controller\Pub\Home;

use App\Repository\ActivityCategoryRepository;
use App\Repository\BankRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class HomeController extends AbstractController
{
    private $translator;
    private $bankRepository;

    public function __construct(TranslatorInterface $translator, BankRepository $bankRepository){
        $this->translator = $translator;
        $this->bankRepository = $bankRepository;
    }

    /**
     * @Route("/", name="home", methods={"GET", "POST"})
     */
    public function index(ActivityCategoryRepository $activityCategoryRepository)
    {
        $user = $this->getUser();

        if(!is_null($user)){
            return $this->redirectToRoute('app_activity');
        }

        $activityCategories = $activityCategoryRepository->findAll();

        return $this->render('public/home/index.html.twig',[
            'activityCategories'=>$activityCategories
        ]);
    }

    /**
     * @Route("/home/contact", name="home_contact", methods={"POST"})
     */
    public function home_contact(Request $request, MailerInterface $mailer)
    {
        $data = $request->request->all();

        if(!isset($data["terms"]) || !$data["terms"]){
            return $this->json(["error"=>true]);
        }

        $name = $data["name"];
        $email = $data["email"];
        $phone = $data["phone"];
        $select = $data["select"];

        $bank = $this->bankRepository->find(1);

        $email = (new TemplatedEmail())
            ->from(new Address($bank->getSenderEmail(), $bank->getSenderName()))
            ->to($bank->getContactEmail())
            ->subject($this->translator->trans('HOME_CONTACT_EMAIL_SUBJECT'))
            ->htmlTemplate('public/home/partials/email.html.twig')
            ->context([
                'cname' => $name,
                'cemail' => $email,
                'cphone' => $phone,
                'cselect' => $select
            ])
        ;

        $mailer->send($email);

        return $this->json(["error"=>false]);
    }
}