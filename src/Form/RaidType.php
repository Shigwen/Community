<?php

namespace App\Form;

use App\Entity\Raid;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

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
					'25' => 25,
					'40' => 40,
				],

				'choice_attr' => function(){
					return ['class' => 'btn-check'];
				},

				'label' => 'Raid type',
				'label_attr' => [
					'class' => 'h5',
				],

				'expanded' => true,
				'multiple' => false,
			])

			->add('raidCharacter', RaidCharacterType::class, [
				'user' => $options['user'],
				'label' => false,
				'mapped' => false,
			])

            ->add('startAt', DateTimeType::class, [
				'widget' => 'single_text',
				'label' => 'Raid starts at :',
				'label_attr' => [
					'class' => 'h5',
				],
			])

            ->add('endAt', DateTimeType::class, [
				'widget' => 'single_text',
				'label' => 'Raid ends at :',
				'label_attr' => [
					'class' => 'h5',
				],
			])

			->add('expectedAttendee', null, [
				'label' => 'Amount of raiders you\'re looking for',
				'label_attr' => [
					'class' => 'h5',
				],
				'attr' => [
					'class' => 'col-2 form-control',
				],
			])

            ->add('minTank', null, [
				'label' => 'Min. number of Tanks (default : 1)',
				'label_attr' => [
					'class' => 'h5',
				],
				'attr' => [
					'class' => 'col-2 form-control',
				],
			])

            ->add('maxTank', null, [
				'label' => 'Max. number of Tanks',
				'label_attr' => [
					'class' => 'h5',
				],
				'attr' => [
					'class' => 'col-2 form-control',
				],
			])

            ->add('minHeal', null, [
				'label' => 'Min. number of Healers (default : 1)',
				'label_attr' => [
					'class' => 'h5',
				],
				'attr' => [
					'class' => 'col-2 form-control',
				],
			])

            ->add('maxHeal', null, [
				'label' => 'Max. number of Healers',
				'label_attr' => [
					'class' => 'h5',
				],
				'attr' => [
					'class' => 'col-2 form-control',
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

            ->add('information', TextareaType::class, [
				'label' => 'Raid Leader notes',
				'label_attr' => [
					'class' => 'h5',
				],
				'attr' => [
					'class' => 'form-control',
					'rows' => '14',
				],
				'data' => 'truc',
			])

			->add('save', SubmitType::class, [
				'label' => $options['isEdit'] ? 'Modify raid' : 'Create raid',
			]);

			if (!$options['isEdit'] && !$options['raidTemplate']) {
				$builder
					->add('templateName', TextType::class, [
						'mapped' => false,
						'label' => 'Give it a template name :',
						'label_attr' => [
							'class' => 'h5',
						],
						'attr' => [
							'class' => 'form-control',
							'placeholder' => 'ex: Taverns of Time - Wednesday Pug - Karazhan',
						],
					])
					->add('saveTemplate', SubmitType::class, [
						'label' => 'Save template',
						'attr' => [
							'class' => 'btn btn-lg btn-primary',
						],
					]);
			}

			if ($options['raidTemplate']) {
				$builder
					->add('templateName', TextType::class, [
						'mapped' => false,
						'data' => $options['raidTemplate']->getName(),
					])
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
			'isEdit' => false,
			'raidTemplate' => null,
		]);
    }
}
