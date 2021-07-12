<?php

namespace App\Form;

use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // For the API
        if ($options['country']) {
            $builder
                ->add('timezone', null, [
                    'label' => 'Timezone',
                    'attr' => [
                        'class' => 'form-control mb-0',
                    ],
                    'query_builder' => function (EntityRepository $er) use ($options) {
                        $country = "%" . $options['country'] . "/%";
                        return $er->createQueryBuilder('t')
                            ->where('t.name LIKE :country')
                            ->setParameter('country', $country);
                    },
                ]);
            return;
        }

        if (!$options['isEdit']) {
            $builder
                ->add('name', null, [
                    'label' => 'Nickname',
                    'attr' => [
                        'placeholder' => 'Diana384',
                        'class' => 'form-control mb-0',
                    ],
                ])
                ->add('email', null, [
                    'label' => $options['isEdit'] ? 'New email' : 'Email',
                    'label_attr' => [
                        'class' => 'h5',
                    ],
                    'attr' => [
                        'class' => 'form-control mb-0',
                    ],
                ]);
        }

        $builder
            ->add('country', ChoiceType::class, [
                'mapped' => false,
                'label' => 'Country',
                'choices' => [
                    'Africa' => 'Africa',
                    'America' => 'America',
                    'Antarctica' => 'Antarctica',
                    'Arctic' => 'Arctic',
                    'Asia' => 'Asia',
                    'Atlantic' => 'Atlantic',
                    'Australia' => 'Australia',
                    'Europe' => 'Europe',
                    'Indian' => 'Indian',
                    'Pacific' => 'Pacific',
                ],
                'attr' => [
                    'class' => 'form-control mb-0',
                ],
            ])
            ->add('timezone', null, [
                'label' => 'Timezone',
                'attr' => [
                    'class' => 'form-control mb-0',
                ],
            ])
            ->add('password', RepeatedType::class, [
                'required' => $options['isEdit'] ? false : true,
                'type' => PasswordType::class,
                'invalid_message' => 'Passwords do not match.',
                'first_options'  => [
                    'label' => $options['isEdit'] ? 'New password' : 'Password',
                    'attr' => [
                        'class' => 'form-control mb-0',
                        'placeholder' => $options['isEdit'] ? 'Let this input empty if you want to keep your old password' : 'Password',
                    ],
                    'constraints' => $options['isEdit'] ? [
                        new Regex([
                            "pattern" => "/^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$/",
                            "message" => "Your password must contain minimum 8 characters, at least one uppercase, one lowercase, one number and one special character"
                        ])
                    ] : [
                        new NotBlank(['message' => 'The password cannot be blank']),
                        new Regex([
                            "pattern" => "/^\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])\S*$/",
                            "message" => "Your password must contain minimum 8 characters, at least one uppercase, one lowercase, one number and one special character"
                        ])
                    ],
                ],
                'second_options' => [
                    'label' => $options['isEdit'] ? 'Confirm new password' : 'Confirm password',
                    'attr' => [
                        'class' => 'form-control mb-0',
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
            'country' => null,
        ]);
    }
}
