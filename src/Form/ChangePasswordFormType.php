<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

class ChangePasswordFormType extends AbstractType
{
    private $translator;

    public function __construct(TranslatorInterface $translator){
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
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
                    'label' => $this->translator->trans("RESET_PASSWORD_FORM_LABEL_1"),
                ],
                'second_options' => [
                    'label' =>  $this->translator->trans("RESET_PASSWORD_FORM_LABEL_2"),
                ],
                'invalid_message' => $this->translator->trans("RESET_PASSWORD_FORM_ERROR_3"),
                // Instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
