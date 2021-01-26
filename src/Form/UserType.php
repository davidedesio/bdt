<?php

namespace App\Form;

use App\Entity\Skill;
use App\Entity\User;
use App\Repository\SkillRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserType extends AbstractType
{
    private $skillRepository;
    private $translator;

    public function __construct(SkillRepository $skillRepository, TranslatorInterface $translator){
        $this->skillRepository = $skillRepository;
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //$skills = $this->skillRepository->findAll();

        $builder
            ->add('name',TextType::class, ['label'=>false])
            ->add('surname',TextType::class, ['label'=>false])
            ->add('fiscal_code',TextType::class, ['label'=>false,'required'=>false])
            ->add('description',TextareaType::class, ['label'=>false,'required'=>false])
            ->add('address',TextType::class, ['label'=>false,'required'=>false])
            ->add('city',TextType::class, ['label'=>false,'required'=>false])
            ->add('country',TextType::class, ['label'=>false,'required'=>false])
            ->add('zip_code',TextType::class, ['label'=>false,'required'=>false])
            ->add('job',TextType::class, ['label'=>false,'required'=>false])
            ->add('hobbies',TextareaType::class, ['label'=>false,'required'=>false])
            ->add('birth_date', DateType::class, ['label'=>false,'widget' => 'single_text','html5' => false,'format' => 'dd/MM/yyyy','required'=>false])
            ->add('phone',TextType::class, ['label'=>false,'required'=>false])
            ->add('driving_licence', CheckboxType::class, ['label'=>false,'required'=>false]) //handled as toggle
            /*
            ->add('skills', EntityType::class, [
                'class' => Skill::class,
                'multiple' => true,
                'choices'  => $skills,
                'required' => false
            ])
            */
            ->add('upload',FileType::class, [
                'mapped'=> false,
                'required'=> false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'maxSizeMessage' => $this->translator->trans('PROFILE_PAGE_UPLOAD_MAX_SIZE_ERROR'),
                        'mimeTypes' => [
                            'image/*',
                        ],
                        'mimeTypesMessage' => $this->translator->trans('PROFILE_PAGE_UPLOAD_WRONG_TYPE_ERROR'),
                    ])
                ],
                'attr'=>[
                    'accept'=>'image/jpeg,image/jpg,image/png'
                ]
            ])
            ->add('picture',HiddenType::class, [
                'mapped'=> true,'required'=>false
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => $this->translator->trans("RESET_PASSWORD_FORM_ERROR_3"),
                'required' => false,
                'first_options'  => [
                    'attr' => array(
                        'placeholder' => $this->translator->trans("PASSWORD")
                    ),
                    'constraints' => [
                        new Length([
                            'min' => 6,
                            'minMessage' => $this->translator->trans("RESET_PASSWORD_FORM_ERROR_2"),
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ]),
                    ],
                    'label'=>false
                ],
                'second_options' => [
                    'attr' => array(
                        'placeholder' => $this->translator->trans("REPEAT_PASSWORD")
                    ),
                    'label'=>false
                ],
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
