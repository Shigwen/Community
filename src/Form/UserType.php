<?php

namespace App\Form;

use App\Entity\User;
use App\Validator\UniqueEmail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!$options['isEdit']) {
            $builder
                ->add('name', null, [
                    'label' => 'Nickname',
                    'attr' => [
                        'placeholder' => 'Diana384',
                        'class' => 'form-control',
                    ],
                ]);
        }

        $builder
            ->add('email', null, [
                'label' => $options['isEdit'] ? 'New email' : 'Email',
                'label_attr' => [
                    'class' => 'h5',
                ],
                'attr' => [
                    'class' => 'form-control',
                ],
                'constraints' => [
                    new UniqueEmail(),
                ]
            ])
            ->add('password', RepeatedType::class, [
                'required' => $options['isEdit'] ? false : true,
                'type' => PasswordType::class,
                'invalid_message' => 'Passwords do not match.',
                'first_options'  => [
                    'label' => 'New password',
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => $options['isEdit'] ? 'Let this input empty if you want to keep your old password' : 'Password',
                    ],
                    'constraints' => $options['isEdit'] ? [] : [
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
                        'placeholder' =>  $options['isEdit'] ? 'Let this input empty if you want to keep your old password' : 'Confirm password',
                    ],
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'isEdit' => false,
        ]);
    }
}
