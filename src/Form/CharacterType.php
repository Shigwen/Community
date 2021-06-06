<?php

namespace App\Form;

use App\Entity\Role;
use App\Entity\Character;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CharacterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, [
                'label' => 'Name',
                'label_attr' => [
                    'class' => 'h5',
                ],
                'attr' => [
                    'class' => 'form-control',
                ]
            ]);

        if (!$options['isSubscribeInARaid']) {
            $builder
                ->add('server', null, [
                    'label' => 'Server',
                    'label_attr' => [
                        'class' => 'h5',
                    ],
                    'attr' => [
                        'class' => 'custom-select',
                    ]
                ])
                ->add('faction', ChoiceType::class, [
                    'choices'  => [
                        Character::FACTION_ALLIANCE => Character::FACTION_ALLIANCE,
                        Character::FACTION_HORDE => Character::FACTION_HORDE,
                    ],
                    'label' => 'Faction',
                    'label_attr' => [
                        'class' => 'h5',
                    ],
                    'attr' => [
                        'class' => 'form-control'
                    ],
                    'expanded' => false,
                    'multiple' => false,
                ]);
        }

        $builder
            ->add('characterClass', null, [
                'label' => 'Class',
                'label_attr' => [
                    'class' => 'h5',
                ],
                'attr' => [
                    'class' => 'custom-select',
                ]
            ])

            ->add('roles', EntityType::class, [
                'label' => 'Roles (you can AND accept to play if asked to)',
                'label_attr' => [
                    'class' => 'h5',
                ],
                'attr' => [
                    'class' => 'form-check',
                ],
                'class' => Role::class,
                'choice_attr' => function () {
                    return ['class' => 'btn-check'];
                },
                'expanded' => true,
                'multiple' => true
            ])

            ->add('information', null, [
                'label' => 'Character notes (anything relevant you\'d like to show to the raid leaders)',
                'label_attr' => [
                    'class' => 'h5',
                ],
                'attr' => [
                    'class' => 'form-control',
                    'rows' => '3',
                ],
                'required' => false,
            ])

            ->add('button', SubmitType::class, [
                'label' => $options['isEdit'] ? 'Modify' : 'Create',
                'attr' => [
                    'class' => 'btn btn-primary rounded-pill btn-lg',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'isEdit' => false,
            'data_class' => Character::class,
            'isSubscribeInARaid' => false,
        ]);
    }
}
