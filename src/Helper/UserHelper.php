<?php


namespace App\Helper;


use App\Entity\User;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserHelper
{
    private $translator;
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function computeStatitics(User $user){

        /* Compute user statistics */
        $sentTransactions = $user->getSentTransactions();
        $askedActivities = count($sentTransactions); //Asked activites for sent transactions

        $sentTransactionsTotalValue = 0;
        foreach($sentTransactions as $sentTransaction){
            $sentTransactionsTotalValue += $sentTransaction->getValue();
        }
        $receivedTransactions = $user->getReceivedTransactions();

        $receivedTransactionsTotalValue = 0;
        $receivedTransactionsCount = 0;
        foreach($receivedTransactions as $receivedTransaction){
            $receivedTransactionsTotalValue += $receivedTransaction->getValue();
            if(!is_null($receivedTransaction->getUserFrom())){
                $receivedTransactionsCount++;
            }
        }
        $doneActivities = $receivedTransactionsCount; //Done activities for received transactions

        $involvedActivites = $askedActivities+$doneActivities; //Total activities in which user is involved
        $totalCredit = $receivedTransactionsTotalValue-$sentTransactionsTotalValue; //Credit = received - sent

        $totalPoint = ($receivedTransactionsCount*10) + (count($sentTransactions)*5);

        $level = [
            "extended"=>"Principiante",
            "next"=>"Apprendista",
            "limit"=> 11,
            "letter"=>"N",
            "icon"=>"fa-mask",
            "points"=>$totalPoint
        ];
        switch (true) {
            case $totalPoint > 10 && $totalPoint<=50:
                $level = [
                    "extended"=>"Apprendista",
                    "next"=>"Maestro",
                    "limit"=> 51,
                    "letter"=>"R",
                    "icon"=>"fa-shield-alt",
                    "points"=>$totalPoint
                ];
                break;

            case $totalPoint > 50 && $totalPoint<=100:
                $level = [
                    "extended"=>"Maestro",
                    "next"=>"Gran Maestro",
                    "limit"=> 101,
                    "letter"=>"C",
                    "icon"=>"fa-shield-alt",
                    "points"=>$totalPoint
                ];
                break;

            case $totalPoint > 100 && $totalPoint<=200:
                $level = [
                    "extended"=>"Gran Maestro",
                    "next"=>"Eroe",
                    "limit"=> 201,
                    "letter"=>"S",
                    "icon"=>"fa-star",
                    "points"=>$totalPoint
                ];
                break;

            case $totalPoint > 200 && $totalPoint<=350:
                $level = [
                    "extended"=>"Eroe",
                    "next"=>"Super Eroe",
                    "limit"=> 351,
                    "letter"=>"H!",
                    "icon"=>"fa-heart",
                    "points"=>$totalPoint
                ];
                break;

            case $totalPoint > 350:
                $level = [
                    "extended"=>"Super Eroe",
                    "letter"=>"SH!",
                    "icon"=>"fa-gem",
                    "points"=>$totalPoint
                ];
                break;
        }

        return $statistics =[
            'activities'=>[
                'done'=> $doneActivities,
                'asked'=> $askedActivities,
                'involved'=>$involvedActivites
            ],
            'transactions'=>[
                'sent'=>$sentTransactionsTotalValue,
                'received'=>$receivedTransactionsTotalValue,
                'credit'=>$totalCredit
            ],
            'level'=>$level,
            "rating"=>$user->getRating()
        ];
    }

    public function getTransactions(User $user){
        $sentTransactions = [];
        foreach($user->getSentTransactions() as $transaction){
            $sentTransactions[]=[
                'userFrom'=>$transaction->getUserFrom()->getName()." ".$transaction->getUserFrom()->getSurname(),
                'userTo'=>$transaction->getUserTo()->getName()." ".$transaction->getUserTo()->getSurname(),
                'value'=>$transaction->getValue(),
                'timestamp'=>$transaction->getCreateTimestamp()
            ];
        }
        $sentTransactions = array_reverse($sentTransactions);

        $receivedTransactions = [];
        foreach($user->getReceivedTransactions() as $transaction){
            $receivedTransactions[]=[
                'userFrom'=>!is_null($transaction->getUserFrom())?$transaction->getUserFrom()->getName()." ".$transaction->getUserFrom()->getSurname():$this->translator->trans('BANK_NAME'),
                'userTo'=>$transaction->getUserTo()->getName()." ".$transaction->getUserTo()->getSurname(),
                'value'=>$transaction->getValue(),
                'timestamp'=>$transaction->getCreateTimestamp()
            ];
        }
        $receivedTransactions = array_reverse($receivedTransactions);

        return [$sentTransactions,$receivedTransactions];
    }
}