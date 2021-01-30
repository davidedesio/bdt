<?php

namespace App\Form;

use App\Entity\Activity;
use App\Entity\ActivityCategory;
use App\Entity\User;
use App\Repository\ActivityCategoryRepository;
use App\Repository\ActivityTypeRepository;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActivityForAdminType extends AbstractType
{
    private $activityTypeRepository;
    private $activityCategoryRepository;
    private $userRepository;

    public function __construct(
        ActivityTypeRepository $activityTypeRepository,
        ActivityCategoryRepository $activityCategoryRepository,
        UserRepository $userRepository
    ){
        $this->activityTypeRepository = $activityTypeRepository;
        $this->activityCategoryRepository = $activityCategoryRepository;
        $this->userRepository = $userRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $activityTypes = $this->activityTypeRepository->findAll();
        $activityCategories = $this->activityCategoryRepository->findAll();
        $users = $this->userRepository->findBy(["_del"=>0]);

        $builder
            ->add('createUser', EntityType::class, [
                'class' => User::class,
                'choices'  => $users,
            ])
            ->add('category', EntityType::class, [
                'class' => ActivityCategory::class,
                'choices'  => $activityCategories,
            ])
            ->add('type', EntityType::class, [
                'class' => \App\Entity\ActivityType::class,
                'choices'  => $activityTypes,
            ])
            ->add('estimated_value',IntegerType::class, [
                'attr' => [
                    'min' => 1,
                    'max' => 5
                ],
                'label'=>false
            ])
            ->add('date', DateType::class, [
                'required'=>true,
                'label'=>false,
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'dd/MM/yyyy',
                'attr'=>[
                    'autocomplete' => 'off'
                ]
            ])
            ->add('time', TimeType::class, [
                'required'=>true,
                'label'=>false,
                'widget' => 'single_text',
                'html5' => false,
                'minutes'=>[0,15,30,45],
                'attr'=>[
                    'autocomplete' => 'off'
                ]
            ])
            ->add('location',TextType::class, [
                'label'=>false,
                'attr'=>[
                    'autocomplete' => 'off'
                ]
            ])
            ->add('description',TextType::class, [
                'label'=>false,
                'attr'=>[
                    'autocomplete' => 'off'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Activity::class,
        ]);
    }
}
