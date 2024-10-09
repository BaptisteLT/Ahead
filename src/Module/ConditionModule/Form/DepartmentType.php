<?php
namespace App\Module\ConditionModule\Form;

use Symfony\Component\Form\AbstractType;
use App\Module\MapModule\Entity\Department;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Module\MapModule\Repository\DepartmentRepository;

class DepartmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('department', EntityType::class, [
                'label' => 'Votre département',
                'class' => Department::class, // The entity class
                'query_builder' => function (DepartmentRepository $er) {
                    // Use a QueryBuilder to customize the query
                    return $er->createQueryBuilder('d')
                        ->orderBy('d.name', 'ASC');
                },
                'placeholder' => 'Liste de départements', // Optional placeholder
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
