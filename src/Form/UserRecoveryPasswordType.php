<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class UserRecoveryPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Passwords do not match.',
                'first_options'  => [
                    'label' => 'New password',
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'Password',
                    ],
                    'constraints' => [
                        new NotBlank(['message' => 'The password cannot be blank']),
                        new Regex([
                            "pattern" => "/^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$/",
                            "message" => "Your password must contain minimum 8 characters, at least one uppercase, one lowercase, one number and one special character"
                        ])
                    ],
                ],
                'second_options' => [
                    'label' => 'Confirm new password',
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'Confirm password',
                    ],
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
