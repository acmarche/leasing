<?php

namespace AcMarche\Leasing\Form;

use AcMarche\Leasing\Entity\KilometerDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class KilometerForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'number_kilometers',
                IntegerType::class,
                [
                    'required' => true,
                    'label' => 'Nombre de km aller/retour',
                    'attr' => ['autocomplete' => 'off'],
                ],
            )
            ->add(
                'number_trips',
                IntegerType::class,
                [
                    'required' => true,
                    'attr' => ['autocomplete' => 'off'],
                    'label' => 'Nombre de trajet par mois',
                ],
            )
            ->add(
                'number_months',
                IntegerType::class,
                [
                    'required' => true,
                    'attr' => ['autocomplete' => 'off'],
                    'label' => 'Nombre de mois dans l\'année',
                ],
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => KilometerDto::class,
            ],
        );
    }

}
