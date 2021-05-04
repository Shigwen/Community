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
				'expanded' => true,
				'multiple' => false,
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
            ->add('autoAccept')
			->add('save', SubmitType::class, [
				'label' => $options['isEdit'] ? 'Modify raid' : 'Create raid',
			]);

			if (!$options['isEdit'] && !$options['raidTemplate']) {
				$builder
					->add('templateName', TextType::class, [
						'mapped' => false,
					])
					->add('saveTemplate', SubmitType::class, [
						'label' => 'Save template'
					]);
			}

			if ($options['raidTemplate']) {
				$builder
					->add('templateName', TextType::class, [
						'mapped' => false,
						'data' => $options['raidTemplate']->getName(),
					])
					->add('editTemplate', SubmitType::class, [
						'label' => 'Edit template'
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
