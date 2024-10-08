<?php
namespace App\Module\MapModule\Form;

use App\Module\ConditionModule\Entity\Disease;
use App\Module\ConditionModule\Entity\Symptoms;
use App\Module\ConditionModule\Repository\DiseaseRepository;
use App\Module\ConditionModule\Repository\SymptomsRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class FiltersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $today = new \DateTime();
        $oneMonthAgo = (clone $today)->modify('-1 month'); // Get the date one month ago

        $builder
            ->add('dateFrom', DateType::class, [
                'label' => 'Date du',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'xx/xx/xxxx',
                    'aria-label' => "Date du (format: jour/mois/année)"
                ],
                'data' => $oneMonthAgo, // Set to midnight
                'widget' => 'single_text', // Use single input field
                'html5' => false, // Disable HTML5 date picker
                'format' => 'dd/MM/yyyy', // Set date format to dd/MM/yyyy
            ])
            ->add('dateTo', DateType::class, [
                'label' => 'Date au',
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
            ->add('symptoms', EntityType::class, [
                'label' => 'Liste de symtômes',
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
            ->add('diseases', EntityType::class, [
                'label' => 'Liste de maladies',
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
