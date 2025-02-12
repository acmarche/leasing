<?php

namespace AcMarche\Leasing\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LeasingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'statut',
                ChoiceType::class,
                [
                    'required' => true,
                    'expanded' => true,
                    'choices' => ['Contractuel' => 'contractuel', 'Statutaire (Nommé)' => 'statutaire'],
                    'label' => 'Votre statut',
                    'placeholder' => 'Choisir le statut',
                ],
            )
            ->add(
                'leasingValue',
                MoneyType::class,
                [
                    'required' => true,
                    'attr' => ['autocomplete' => 'off'],
                    'label' => 'Valeur d\'achat du vélo',
                ],
            )
            ->add(
                'accessoryValue',
                MoneyType::class,
                [
                    'required' => true,
                    'attr' => ['autocomplete' => 'off'],
                    'label' => 'Accessoires et abonnements',
                    'help' => 'Mettre ici le reste des accessoires (cadenas, casque,...) ou abonnements divers.',
                ],
            )
            ->add(
                'grossAnnualFull',
                MoneyType::class,
                [
                    'label' => 'Brut annuel à 100%',
                    'help' => '<strong>Code 9001</strong>, prendre le montant se trouvant juste à gauche de "Index".',
                    'help_html' => true,
                    'attr' => ['autocomplete' => 'off'],
                ],
            )
            ->add(
                'monthlyHouseholdResidency',
                MoneyType::class,
                [
                    'label' => 'Foyer-Résidentielle. mensuelle',
                    'help' => '<strong>Codes 3200 ou 3210</strong>. Si aucun de ces deux codes n\'est présent, mettre 0,00',
                    'help_html' => true,
                    'attr' => ['autocomplete' => 'off'],
                ],
            )
            ->add(
                'taxableAnnualAFA',
                MoneyType::class,
                [
                    'label' => 'Imposable annuel AFA',
                    'help' => '<strong>Code 9213</strong>.',
                    'help_html' => true,
                    'attr' => ['autocomplete' => 'off'],
                    'scale' => 2,
                ],
            )
            ->add(
                'indexValue',
                NumberType::class,
                [
                    'label' => 'Index',
                    'help' => '<strong>Code 9001</strong>, prendre le montant se trouvant juste à droite de "Index".',
                    'help_html' => true,
                    'attr' => ['autocomplete' => 'off'],
                ],
            )
            ->add(
                'workRegime',
                NumberType::class,
                [
                    'label' => 'Régime de travail en heures semaine',
                    'help' => 'Donnée disponible dans l\'encart reprenant vos informations personnelles.',
                    'help_html' => true,
                    'attr' => ['autocomplete' => 'off'],
                ],
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => LeasingData::class,
            ],
        );
    }

}
