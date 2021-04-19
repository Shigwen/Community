<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email')
            ->add('password', RepeatedType::class, [
				'required' => false,
				'type' => PasswordType::class,
				'invalid_message' => 'Les mots de passe saisis ne correspondent pas',
				'first_options'  => [
					'label' => 'Mot de passe',
					'attr' => [
						'placeholder' => 'Laisser vide si inchangé',
					],
				],
				'second_options' => [
					'label' => 'Vérification du mot de passe',
					'attr' => [
						'placeholder' => 'Laisser vide si inchangé',
					],
				],
				'empty_data' => '',
			]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
