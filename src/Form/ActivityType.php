<?php

namespace App\Form;

use App\Entity\Activity;
use App\Entity\ActivityCategory;
use App\Repository\ActivityCategoryRepository;
use App\Repository\ActivityTypeRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class ActivityType extends AbstractType
{
    private $activityTypeRepository;
    private $activityCategoryRepository;
    private $translator;
    private $security;

    public function __construct(ActivityTypeRepository $activityTypeRepository, ActivityCategoryRepository $activityCategoryRepository, TranslatorInterface  $tranlsator, Security $security){
        $this->activityTypeRepository = $activityTypeRepository;
        $this->activityCategoryRepository = $activityCategoryRepository;
        $this->translator = $tranlsator;
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $activityTypes = $this->activityTypeRepository->findAll();
        $activityCategories = $this->activityCategoryRepository->findAll();


        $builder
            ->add('category', EntityType::class, [
                'class' => ActivityCategory::class,
                'choices'  => $activityCategories,
            ])
            ->add('type', EntityType::class, [
                'class' => \App\Entity\ActivityType::class,
                'choices'  => $activityTypes,
            ])
            ->add('estimated_value',TextType::class, ['label'=>false])
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
                    'autocomplete' => 'off',
                    'placeholder'=>$this->translator->trans('ACTIVITY_PAGE_INSERT')." ".$this->security->getUser()->getName()."?"
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
