<?php

namespace AcMarche\Leasing\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class LeasingSendType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'nom',
                TextType::class,
                [
                    'required' => true,
                    'attr' => ['autocomplete' => 'off'],
                ],
            )
            ->add(
                'prenom',
                TextType::class,
                [
                    'required' => true,
                    'label' => 'Prénom',
                    'attr' => ['autocomplete' => 'off'],
                ],
            )
            ->add(
                'service',
                TextType::class,
                [
                    'required' => true,
                    'help' => 'Dans quel service travaillez vous',
                ],
            )
            ->add(
                'email',
                EmailType::class,
                [
                    'required' => true,
                    'help' => 'Vous recevrez une copie',
                ],
            );
    }
}
