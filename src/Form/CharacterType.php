<?php

namespace App\Form;

use App\Entity\Character;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CharacterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, [
				'label' => 'Nom',
				'label_attr' => [
					'id' => 're',
					'class' => 'CUSTOM_LABEL_CLASS',
				],
				'attr' => [
					'class' => 'coucou comment ça va',
				]
			])
            ->add('information', null, [
				'label' => 'Informations',
			])
            ->add('characterClass', null, [
				'label' => 'Classe',
			])
            ->add('server', null, [
				'label' => 'Serveur',
			])
            ->add('roles', null, [
				'label' => 'Roles',
			])
			->add('button', SubmitType::class, [
				'label' => $options['isEdit'] ? 'Modify' : 'Create',
			]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
			'isEdit' => false,
            'data_class' => Character::class,
        ]);
    }
}
