<?php

namespace App\Form;

use App\Entity\Message;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, [
                'label' => 'Your name',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('email', null, [
                'label' => 'Your email',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('messageType', null, [
                'label' => 'Subject',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('message', null, [
                'label' => 'Your message',
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 6,
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
        ]);
    }
}
