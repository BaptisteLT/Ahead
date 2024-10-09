<?php
namespace App\Module\ConditionModule\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class DiseaseDiagnosedType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('diagnosed', ChoiceType::class, [
                'label' => 'Votre maladie a-t-elle été diagnostiquée?',
                'choices' => [
                    'Oui' => true,
                    'Non' => false,
                ],
                'expanded' => true, // Renders as radio buttons
                'multiple' => false, // Single choice only
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([

        ]);
    }
}