<?php
namespace App\Module\ConditionModule\Form;

use App\Module\ConditionModule\Entity\Disease;
use App\Module\ConditionModule\Repository\DiseaseRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchDiseaseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('diseases', EntityType::class, [
                'label' => 'Rechercher une maladie',
                'class' => Disease::class, // The entity class
                'choice_label' => 'name', // What will be displayed in the dropdown
                'query_builder' => function (DiseaseRepository $er) {
                    // Use a QueryBuilder to customize the query
                    return $er->createQueryBuilder('d')
                        ->orderBy('d.name', 'ASC');
                },
                'placeholder' => 'Liste de maladies', // Optional placeholder
                'multiple' => false, // You can set to true for multiple selection
                'autocomplete' => true,
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
