<?php

namespace App\Form;

use App\Entity\Raid;
use App\Form\RaidCharacterType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class RaidType extends AbstractType
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
                ],
            ])
            ->add('raidType', ChoiceType::class, [
                'choices'  => [
                    '10' => 10,
                    '20' => 20,
                    '25' => 25,
                    '40' => 40,
                ],

                'label' => 'Raid type',
                'label_attr' => [
                    'class' => 'h5',
                ],

                'choice_attr' => function () {
                    return ['class' => 'btn-check'];
                },

                'expanded' => true,
                'multiple' => false,
            ])

            ->add('raidCharacter', RaidCharacterType::class, [
                'user' => $options['user'],
                'label' => false,
                'mapped' => false,
            ])

            ->add('startAt', DateTimeType::class, [
                'widget' => 'choice',
                'minutes' => [0, 15, 30, 45],
                'label' => 'Raid starts at (local time)',
                'label_attr' => [
                    'class' => 'h5',
                ],
                'view_timezone' => $options['user']->getTimezone()->getName(),
            ])

            ->add('endAt', DateTimeType::class, [
                'widget' => 'choice',
                'minutes' => [0, 15, 30, 45],
                'label' => 'Raid ends at (local time)',
                'label_attr' => [
                    'class' => 'h5',
                ],
                'view_timezone' => $options['user']->getTimezone()->getName(),
            ])

            ->add('expectedAttendee', null, [
                'label' => 'Amount of raiders you\'re looking for',
                'label_attr' => [
                    'class' => 'h5',
                ],
                'attr' => [
                    'class' => 'col-2 form-control text-center',
                ],
            ])

            ->add('minTank', null, [
                'label' => 'Min. number of Tanks (default : 1)',
                'label_attr' => [
                    'class' => 'h5',
                ],
                'attr' => [
                    'class' => 'col-2 form-control text-center',
                ],
                'empty_data' => '1',
            ])

            ->add('maxTank', null, [
                'label' => 'Max. number of Tanks',
                'label_attr' => [
                    'class' => 'h5',
                ],
                'attr' => [
                    'class' => 'col-2 form-control text-center',
                ],
            ])

            ->add('minHeal', null, [
                'label' => 'Min. number of Healers (default : 1)',
                'label_attr' => [
                    'class' => 'h5',
                ],
                'attr' => [
                    'class' => 'col-2 form-control text-center',
                ],
                'empty_data' => '1',
            ])

            ->add('maxHeal', null, [
                'label' => 'Max. number of Healers',
                'label_attr' => [
                    'class' => 'h5',
                ],
                'attr' => [
                    'class' => 'col-2 form-control text-center',
                ],
            ])

            ->add('autoAccept', null, [
                'label' => 'Auto-accept',
                'label_attr' => [
                    'class' => 'form-check-label h5',
                ],
                'attr' => [
                    'class' => 'form-check-input',
                ],
            ])

            ->add('isPrivate', null, [
                'label' => 'Private raid',
                'label_attr' => [
                    'class' => 'form-check-label h5',
                ],
                'attr' => [
                    'class' => 'form-check-input',
                ],
            ])

            ->add('information', CKEditorType::class, [
                'label' => 'Raid Leader notes',
                'label_attr' => [
                    'class' => 'h5',
                ],
                'attr' => [
                    'class' => 'form-control',
                    'rows' => '14',
                ],
            ]);

        if (!$options['isEdit']) {
            $builder
                ->add('templateName', TextType::class, [
                    'label' => 'Give it a template name :',
                    'label_attr' => [
                        'class' => 'h5',
                    ],
                    'attr' => [
                        'class' => 'form-control',
                        'placeholder' => 'ex: Taverns of Time - Wednesday Pug - Karazhan',
                    ],
                    'required' => false,
                ]);
        }

        if (!$options['isEdit']) {
            $builder
                ->add('saveTemplate', SubmitType::class, [
                    'label' => 'Save template',
                    'attr' => [
                        'class' => 'btn btn-lg btn-primary',
                    ],
                ]);
        }

        if ($options['isRaidTemplate']) {
            $builder
                ->add('editTemplate', SubmitType::class, [
                    'label' => 'Edit template',
                    'attr' => [
                        'class' => 'btn btn-lg btn-primary',
                    ],
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Raid::class,
            'user' => null,
            'raidInformation' => null,
            'isEdit' => false,
            'isRaidTemplate' => false,
        ]);
    }
}
