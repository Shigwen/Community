<?php

namespace App\Form;

use App\Entity\Raid;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

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
            ->add('startAt')
            ->add('endAt')
            ->add('information')
            ->add('minTank')
            ->add('maxTank')
            ->add('minHeal')
            ->add('maxHeal')
            ->add('autoAccept');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Raid::class,
        ]);
    }
}
