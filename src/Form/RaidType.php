<?php

namespace App\Form;

use App\Entity\Raid;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class RaidType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('raidType', ChoiceType::class, [
				'choices'  => [
					'10' => 10,
					'25' => 25,
					'40' => 40,
				],
			])
            ->add('expectedAttendee')
            ->add('startAt', DateTimeType::class, [
				'widget' => 'single_text'
			])
            ->add('endAt', DateTimeType::class, [
				'widget' => 'single_text'
			])
            ->add('information')
            ->add('minTank')
            ->add('maxTank')
            ->add('minHeal')
            ->add('maxHeal')
			->add('raidCharacters', CollectionType::class, [
				'entry_type' => RaidCharacterType::class,
				'entry_options' => [
					'user' => $options['user']
				],
			])
            ->add('autoAccept');

			if (!$options['isEdit']) {
				$builder
					->add('templateName', TextType::class, [
					'mapped' => false,
					])
					->add('dayOfWeek', ChoiceType::class, [
						'mapped' => false,
						'choices'  => [
							'--' => 0,
							'Monday' => 1,
							'Tuesday' => 2,
							'Wednesday' => 3,
							'Thursday' => 4,
							'Friday' => 5,
							'Saturday' => 6,
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
			]);
    }
}
