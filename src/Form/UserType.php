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
		if (!$options['isEdit']) {
			$builder->add('name', null, [
				'label' => 'Nickname',
				'attr' => [
					'placeholder' => 'Diana384',
					'class' => 'form-control mb-1',
				]
			]);
		}

        $builder
            ->add('email', null, [
				'label' => 'Email adress',
				'attr' => [
					'class' => 'form-control mb-1',
				]
			])
            ->add('password', RepeatedType::class, [
				'required' => $options['isEdit'] ? false : true,
				'type' => PasswordType::class,
				'invalid_message' => 'Les mots de passe saisis ne correspondent pas',
				'first_options'  => [
					'label' => 'Password',
					'attr' => [
						'class' => 'form-control mb-1',
						'placeholder' => $options['isEdit'] ? 'Laisser vide si inchangé' : 'Password',
					],
				],
				'second_options' => [
					'label' => 'Confirm password',
					'attr' => [
						'class' => 'form-control mb-1',
						'placeholder' =>  $options['isEdit'] ? 'Laisser vide si inchangé' : 'Repeat password',
					],
				],
				'empty_data' => '',
			]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
			'isEdit' => false,
        ]);
    }
}
