<?php
namespace App\Module\ConditionModule\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class SickDateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $today = new \DateTime();

        $builder
            ->add('date', DateType::class, [
                'label' => 'Quel jour êtes-vous tombé malade?',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'xx/xx/xxxx',
                    'aria-label' => "Date du (format: jour/mois/année)"
                ],
                'data' => $today, // Set to midnight
                'widget' => 'single_text', // Use single input field
                'html5' => false, // Disable HTML5 date picker
                'format' => 'dd/MM/yyyy', // Set date format to dd/MM/yyyy
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
