<?php

namespace App\Form;

use App\Entity\Role;
use App\Entity\Character;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CharacterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // For the API
        if ($options['gameVersion']) {
            $builder
                ->add('server', null, [
                    'label' => 'Server (Europe only)',
                    'label_attr' => [
                        'class' => 'h5',
                    ],
                    'attr' => [
                        'class' => 'custom-select',
                    ],
                    'query_builder' => function (EntityRepository $er) use ($options) {
                        return $er->createQueryBuilder('s')
                            ->join('s.gameVersion', 'gv')
                            ->where('gv.id = :gameVersion')
                            ->setParameter('gameVersion', $options['gameVersion']->getId());
                    },
                ]);

            return;
        }

        $builder
            ->add('name', null, [
                'label' => 'Name',
                'label_attr' => [
                    'class' => 'h5',
                ],
                'attr' => [
                    'class' => 'form-control mb-0',
                ]
            ]);

        if (!$options['isSubscribeInARaid']) {
            $builder
                ->add('gameVersion', ChoiceType::class, [
                    'mapped' => false,
                    'label' => 'Game version',
                    'label_attr' => [
                        'class' => 'h5',
                    ],
                    'choices' => [
                        'Retail' => 1,
                        'Classic' => 2,
                        'TBC Classic' => 3
                    ],
                    'attr' => [
                        'class' => 'form-control mb-0',
                    ],
                ])
                ->add('server', null, [
                    'label' => 'Server (Europe only)',
                    'label_attr' => [
                        'class' => 'h5',
                    ],
                    'attr' => [
                        'class' => 'custom-select',
                    ]
                ])
                ->add('faction', null, [
                    'label' => 'Faction',
                    'label_attr' => [
                        'class' => 'h5',
                    ],
                    'attr' => [
                        'class' => 'form-control'
                    ]
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

            ->add('information', CKEditorType::class, [
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
            'gameVersion' => null,
        ]);
    }
}
