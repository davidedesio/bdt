<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationFormType extends AbstractType
{
    private $translator;

    public function __construct(TranslatorInterface $translator){
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',TextType::class, [
                'attr' => array(
                    'placeholder' => $this->translator->trans("NAME")
                ),
                'label'=>false
            ])
            ->add('surname',TextType::class, [
                'attr' => array(
                    'placeholder' => $this->translator->trans("SURNAME")
                ),
                'label'=>false
            ])
            ->add('email', RepeatedType::class, [
                'type' => EmailType::class,
                'invalid_message' => $this->translator->trans("INVALID_MATCH_EMAIL"),
                'required' => true,
                'first_options'  => [
                    'attr' => array(
                        'placeholder' => $this->translator->trans("EMAIL")
                    ),
                    'label'=>false
                ],
                'second_options' => [
                    'attr' => array(
                        'placeholder' => $this->translator->trans("REPEAT_EMAIL")
                    ),
                    'label'=>false
                ]
            ])
            ->add('phone',TextType::class, [
                'label'=>false,
                'required'=>false,
                'attr' => array(
                    'placeholder' => $this->translator->trans("PHONE")
                )
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => $this->translator->trans("RESET_PASSWORD_FORM_ERROR_3"),
                'required' => true,
                'first_options'  => [
                    'attr' => array(
                        'placeholder' => $this->translator->trans("PASSWORD")
                    ),
                    'constraints' => [
                        new NotBlank([
                            'message' => $this->translator->trans("RESET_PASSWORD_FORM_ERROR_1"),
                        ]),
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

            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'label'=> $this->translator->trans("REGISTER_FORM_AGREE"),
                'constraints' => [
                    new IsTrue([
                        'message' => $this->translator->trans("REGISTER_FORM_AGREE_ERROR"),
                    ]),
                ],
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
