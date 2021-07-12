<?php

namespace App\Form;

use App\Entity\Character;
use App\Entity\RaidCharacter;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RaidCharacterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('userCharacter', EntityType::class, [
                'class' => Character::class,
                'query_builder' => function (EntityRepository $er) use ($options) {
                    $query = $er->createQueryBuilder('uc')
                        ->where('uc.user = :user')
                        ->andWhere('uc.isArchived = 0')
                        ->orderBy('uc.name', 'ASC')
                        ->setParameter('user', $options['user']);

                    if ($options['raidCharacter']) {
                        $query
                            ->andWhere('uc.server = :server')
                            ->andWhere('uc.faction = :faction')
                            ->setParameter('server', $options['raidCharacter']->getUserCharacter()->getServer())
                            ->setParameter('faction', $options['raidCharacter']->getUserCharacter()->getFaction());
                    }
                    return $query;
                },

                'label' => 'I play',
                'label_attr' => [
                    'class' => 'h5',
                ],
                'attr' => [
                    'class' => 'custom-select',
                ],
                'group_by' => function ($character) {
                    return $character->getServer()->getVerboseVersionAndName() . ' - ' . $character->getFaction()->getName();
                },
            ])

            ->add('role', null, [
                'label' => 'With role',
                'label_attr' => [
                    'class' => 'h5',
                ],
                'attr' => [
                    'class' => 'custom-select',
                ],
                'constraints' => [
                    new NotNull(['message' => 'You must specify a role'])
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RaidCharacter::class,
            'user' => null,
            'raidCharacter' => null,
        ]);
    }
}
