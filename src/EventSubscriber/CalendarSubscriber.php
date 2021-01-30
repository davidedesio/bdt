<?php

namespace App\EventSubscriber;

use App\Repository\ActivityRepository;
use CalendarBundle\CalendarEvents;
use CalendarBundle\Entity\Event;
use CalendarBundle\Event\CalendarEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CalendarSubscriber implements EventSubscriberInterface
{
    private $tokenStorage;
    private $activityRepository;
    private $router;

    public function __construct(
        TokenStorageInterface $tokenStorage,
        ActivityRepository $activityRepository,
        UrlGeneratorInterface $router
    ){
        $this->tokenStorage = $tokenStorage;
        $this->activityRepository = $activityRepository;
        $this->router = $router;


    }

    public static function getSubscribedEvents()
    {
        return [
            CalendarEvents::SET_DATA => 'onCalendarSetData',
        ];
    }

    public function onCalendarSetData(CalendarEvent $calendar)
    {
        if (!$token = $this->tokenStorage->getToken()) {
            return ;
        }

        if (!$token->isAuthenticated()) {
            return ;
        }

        if (!$user = $token->getUser()) {
            return ;
        }

        $start = $calendar->getStart();
        $end = $calendar->getEnd();
        $filters = $calendar->getFilters();
        $calendarRole = $filters['calendar-role'];
        $activityTypeFilter = $filters['activity-type'];
        $activityCategoryFilterFilter = $filters['activity-category'];
        $acceptedFilter = $filters["accepted"];

        if($calendarRole=="ADMIN" && in_array("ROLE_ADMIN",$user->getRoles())){

            $activities = $this->activityRepository->findForAdminCalendar([
                "start"=>$start,
                "activityTypeId"=>!empty($activityTypeFilter)?(int)$activityTypeFilter:null,
                "activityCategoryId"=>!empty($activityCategoryFilterFilter)?(int)$activityCategoryFilterFilter:null,
                "accepted"=>$acceptedFilter,
                "end"=>$end
            ]);

        } else {

            $activities = $this->activityRepository->findForCalendar($user,["start"=>$start,"end"=>$end]);

        }

        foreach($activities as $activity){

            $title = "";
            $activityTypeId = $activity->getType()->getId(); // 1 request 2 offer
            $activityCategoryId = $activity->getCategory()->getId();
            $activityCategoryName = strtolower($activity->getCategory()->getNameIt());
            $activityCreateUser = $activity->getCreateUser();
            $activityAcceptedUser = $activity->getAcceptedUser();

            if($calendarRole=="ADMIN" && in_array("ROLE_ADMIN",$user->getRoles())) {

                if($activityTypeId == 1) {
                    if(!is_null($activityAcceptedUser)){
                        $title = $activityCreateUser->getName()." ".$activityCreateUser->getSurname()." riceverà ";
                        $title .= $activityCategoryName;
                        $title .= " da ";
                        $title .= $activityAcceptedUser->getName()." ".$activityAcceptedUser->getSurname();
                    } else {
                        $title = $activityCreateUser->getName()." ".$activityCreateUser->getSurname()." ha richiesto ";
                        $title .= $activityCategoryName;
                    }

                } else if($activityTypeId == 2) {
                    if(!is_null($activityAcceptedUser)){
                        $title = $activityCreateUser->getName()." ".$activityCreateUser->getSurname()." offrirà ";
                        $title .= $activityCategoryName;
                        $title .= " a ";
                        $title .= $activityAcceptedUser->getName()." ".$activityAcceptedUser->getSurname();
                    } else {
                        $title = $activityCreateUser->getName()." ".$activityCreateUser->getSurname()." ha offerto ";
                        $title .= $activityCategoryName;
                    }
                }


            } else {
                if($user==$activityCreateUser){
                    if($activityTypeId == 1) {
                        if(!is_null($activityAcceptedUser)){
                            $title = "Riceverai ";
                            $title .= $activityCategoryName;
                            $title .= " da ";
                            $title .= $activityAcceptedUser->getName();
                        } else {
                            $title = "Hai richiesto ";
                            $title .= $activityCategoryName;
                            $title .= ", ma non hai ancora scelto un aiuto";
                        }

                    } else if($activityTypeId == 2) {
                        if(!is_null($activityAcceptedUser)){
                            $title = "Offrirai ";
                            $title .= $activityCategoryName;
                            $title .= " a ";
                            $title .= $activityAcceptedUser->getName();
                        } else {
                            $title = "Hai offerto ";
                            $title .= $activityCategoryName;
                            $title .= ", ma non hai ancora scelto chi aiutare";
                        }
                    }
                } else if($user==$activityAcceptedUser){
                    if($activityTypeId == 1) {
                        $title = "Offrirai ";
                        $title .= $activityCategoryName;
                        $title .= " a ";
                        $title .= $activityCreateUser->getName();
                    } else if($activityTypeId == 2) {
                        $title = "Riceverai ";
                        $title .= $activityCategoryName;
                        $title .= " da ";
                        $title .= $activityCreateUser->getName();
                    }
                }
            }



            $calendarEvent = new Event(
                $title,
                new \DateTime($activity->getDate()->format("Y-m-d")." ".$activity->getTime()->format("H:i:s")),
                new \DateTime($activity->getDate()->format("Y-m-d")." ".$activity->getTime()->add(new \DateInterval('PT'.$activity->getEstimatedValue().'H'))->format("H:i:s"))
            );



            if(is_null($activity->getAcceptedUser())){
                $calendarEvent->setOptions([
                    'classNames' => ['open'],
                    'id' => $activity->getId(),
                    'icon' => "category-".$activityCategoryId.".svg"
                ]);
            } else {
                $calendarEvent->setOptions([
                    'classNames' => ['closed'],
                    'id' => $activity->getId(),
                    'icon' => "category-".$activityCategoryId.".svg"
                ]);
            }


            if($calendarRole=="ADMIN" && in_array("ROLE_ADMIN",$user->getRoles())) {
                $eventUrl = $this->router->generate('admin_activity_edit', [
                    'id' => $activity->getId(),
                ]);
            } else {
                $eventUrl = $this->router->generate('app_activity_detail', [
                    'id' => $activity->getId(),
                ]);
            }
            $calendarEvent->addOption('url',$eventUrl);
            $calendar->addEvent($calendarEvent);
        }

    }
}