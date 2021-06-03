<?php

namespace App\Form;

use App\Entity\Character;
use App\Entity\RaidCharacter;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RaidCharacterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
			->add('userCharacter', EntityType::class, [
				'class' => Character::class,
				'query_builder' => function (EntityRepository $er) use ($options) {
					return $er->createQueryBuilder('uc')
						->where('uc.user = :user')
						->andWhere('uc.isArchived = 0')
						->orderBy('uc.name', 'ASC')
						->setParameter('user', $options['user']);
				},

				'label' => 'Raid leading character',
				'label_attr' => [
					'class' => 'h5',
				],
				'attr' => [
					'class' => 'custom-select',
				],
			])

            ->add('role', null, [
				'label' => 'Character Role in this raid',
				'label_attr' => [
					'class' => 'h5',
				],
				'attr' => [
					'class' => 'custom-select',
				],
				// not null
			]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RaidCharacter::class,
			'user' => null,
        ]);
    }
}
