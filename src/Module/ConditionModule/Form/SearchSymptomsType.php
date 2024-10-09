<?php
namespace App\Module\ConditionModule\Form;

use App\Module\ConditionModule\Entity\Symptoms;
use App\Module\ConditionModule\Repository\SymptomsRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchSymptomsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('symptoms', EntityType::class, [
                'label' => 'Rechercher vos symtômes',
                'class' => Symptoms::class, // The entity class
                'choice_label' => 'name', // What will be displayed in the dropdown
                'query_builder' => function (SymptomsRepository $er) {
                    // Use a QueryBuilder to customize the query
                    return $er->createQueryBuilder('s')
                        ->orderBy('s.name', 'ASC');
                },
                'placeholder' => 'Liste de symptômes', // Optional placeholder
                'multiple' => false, // You can set to true for multiple selection
                'autocomplete' => true,
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
