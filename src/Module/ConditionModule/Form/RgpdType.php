<?php
namespace App\Module\ConditionModule\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class RgpdType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'En continuant, j’accepte que mes données soient utilisées anonymement dans le but de rencenser les différentes maladies et symptômes présents sur le territoire français.',
                'required' => true,
            ]);
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
