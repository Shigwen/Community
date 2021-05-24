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

            ->add('information', TextareaType::class, [
				'label' => 'Raid Leader notes',
				'label_attr' => [
					'class' => 'h5',
				],
				'attr' => [
					'class' => 'form-control',
					'rows' => '14',
				],
				'data' => 'Raid leading style and goals:

My goal is for everyone to enjoy discovering the raid at its own pace. I will take a few minutes before every boss to explain the strategy and make sure everyone understands what has to be done.
OR
I\'ll try to gather as many good players as I can so we can speed run this with full mats ! High-parsing players will most likely have more priority in this raid than other lower geared characters.

Loot rules :

Reserved > Main-spec > Main-spec but already got an item > Off-spec.
OR
We will be using EPGP / Loot council / DKP / etc.

Mandatory add-ons :

DBM, Angry Assignments, and so on...

Anything else you might think is important to mention :

Strategies will never be debated during the raid, no matter how much one might think he knows better.',
			])

			->add('save', SubmitType::class, [
				'label' => $options['isEdit'] ? 'Modify raid' : 'Post event to calendar',
				'attr' => [
					'class' => 'btn btn-primary rounded-pill btn-lg',
				],
			]);


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
