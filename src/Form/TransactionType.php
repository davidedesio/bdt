<?php

namespace App\Form;

use App\Entity\Transaction;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TransactionType extends AbstractType
{
    private $userRepository;

    public function __construct(
        UserRepository $userRepository
    ){
        $this->userRepository = $userRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $users = $this->userRepository->findBy(["_del"=>0]);

        $builder
            ->add('value')
            ->add('userFrom', EntityType::class, [
                'class' => User::class,
                'choices'  => $users,
                'required' => false
            ])
            ->add('userTo', EntityType::class, [
                'class' => User::class,
                'choices'  => $users,
            ])
            ->add('reason')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Transaction::class,
        ]);
    }
}
