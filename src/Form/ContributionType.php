<?php

declare(strict_types=1);

namespace App\Form;

use App\Config\ContributorRole;
use App\Entity\Contribution;
use App\Entity\Person;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

/**
 * Contribution form.
 */
class ContributionType extends AbstractType {
    /**
     * Add form fields to $builder.
     */
    public function buildForm(FormBuilderInterface $builder, array $options) : void {
        $builder->add('person', Select2EntityType::class, [
            'label' => 'Person',
            'required' => true,
            'class' => Person::class,
            'remote_route' => 'person_typeahead',
            'allow_clear' => true,
            'attr' => [
                'add_path' => 'person_new',
                'add_label' => 'Add Person',
            ],
            'placeholder' => 'Search for an existing person by name',
        ]);

        $builder->add('roles', EnumType::class, [
            'label' => 'Roles',
            'required' => true,
            'multiple' => true,
            'class' => ContributorRole::class,
            'choice_label' => fn (?ContributorRole $role) : string => $role ? $role->label() : '',
            'help' => 'Contributors with the <strong>Author</strong> role will appear in the item\'s citation.',
            'help_html' => true,
            'attr' => [
                'class' => 'select2-simple',
                'data-theme' => 'bootstrap-5',
            ],
            'placeholder' => 'Select contributor roles',
        ]);
    }

    /**
     * Define options for the form.
     *
     * Set default, optional, and required options passed to the
     * buildForm() method via the $options parameter.
     */
    public function configureOptions(OptionsResolver $resolver) : void {
        $resolver->setDefaults([
            'data_class' => Contribution::class,
        ]);
    }
}
