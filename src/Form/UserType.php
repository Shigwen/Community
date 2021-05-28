<?php

namespace App\Form;

use App\Entity\User;
use App\Validator\Constraints\UniqueEmail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
		if (!$options['isEdit']) {
			$builder->add('name', null, [
				'label' => 'Nickname',
				'attr' => [
					'placeholder' => 'Diana384',
					'class' => 'form-control',
				],
				'constraints' => [
					new NotBlank(['message' => 'The name cannot be blank'])
				],
			]);
		}

        $builder
            ->add('email', null, [
				'label' => $options['isEdit'] ? 'New email' : 'Email',
				'label_attr' => [
					'class' => 'h5',
				],
				'attr' => [
					'class' => 'form-control',
				],
				'constraints' => [
					new NotBlank(['message' => 'The email cannot be blank']),
					new Email(['message' => 'The email is not valid']),
					new UniqueEmail(),
				],
			])
            ->add('password', RepeatedType::class, [
				'required' => $options['isEdit'] ? false : true,
				'type' => PasswordType::class,
				'invalid_message' => 'Passwords do not match.',
				'first_options'  => [
					'label' => 'New password',
					'attr' => [
						'class' => 'form-control',
						'placeholder' => $options['isEdit'] ? 'Let this input empty if you want to keep your old password' : 'Password',
					],
					'constraints' => $options['isEdit'] ? [] : [
						new NotBlank(['message' => 'The password cannot be blank'])
					],
				],
				'second_options' => [
					'label' => 'Confirm new password',
					'attr' => [
						'class' => 'form-control',
						'placeholder' =>  $options['isEdit'] ? 'Let this input empty if you want to keep your old password' : 'Confirm password',
					],
					'constraints' => $options['isEdit'] ? [] : [
						new NotBlank(['message' => 'The password cannot be blank'])
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
