<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Category;
use App\Entity\Civilization;
use App\Entity\InscriptionStyle;
use App\Entity\Item;
use App\Entity\Language;
use App\Entity\Location;
use App\Entity\Material;
use App\Entity\Period;
use App\Entity\Subject;
use App\Entity\Technique;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

/**
 * Item form.
 */
class ItemType extends AbstractType {
    /**
     * Add form fields to $builder.
     */
    public function buildForm(FormBuilderInterface $builder, array $options) : void {
        $builder->add('name', TextType::class, [
            'label' => 'Name',
            'required' => true,
        ]);
        $builder->add('category', Select2EntityType::class, [
            'label' => 'Category',
            'class' => Category::class,
            'remote_route' => 'category_typeahead',
            'allow_clear' => true,
            'multiple' => true,
            'attr' => [
                'add_path' => 'category_new',
                'add_label' => 'Add Category',
            ],
            'placeholder' => 'Search for an existing category by name',
        ]);
        $builder->add('description', TextareaType::class, [
            'label' => 'Description',
            'required' => false,
            'attr' => [
                'class' => 'tinymce',
            ],
        ]);
        $builder->add('inscription', TextareaType::class, [
            'label' => 'Inscription',
            'required' => false,
            'attr' => [
                'class' => 'tinymce',
            ],
        ]);
        $builder->add('translatedInscription', TextareaType::class, [
            'label' => 'Inscription Translation',
            'required' => false,
            'attr' => [
                'class' => 'tinymce',
            ],
        ]);
        $builder->add('inscriptionLanguage', Select2EntityType::class, [
            'label' => 'Inscription Language',
            'class' => Language::class,
            'remote_route' => 'language_typeahead',
            'allow_clear' => true,
            'multiple' => true,
            'attr' => [
                'add_path' => 'language_new',
                'add_label' => 'Add Language',
            ],
            'placeholder' => 'Search for an existing language by name',
        ]);
        $builder->add('inscriptionStyle', Select2EntityType::class, [
            'label' => 'Inscription Type',
            'class' => InscriptionStyle::class,
            'remote_route' => 'inscription_style_typeahead',
            'allow_clear' => true,
            'attr' => [
                'add_path' => 'inscription_style_new',
                'add_label' => 'Add Inscr. Type',
            ],
            'placeholder' => 'Search for an existing inscription type by name',
        ]);
        $builder->add('civilization', Select2EntityType::class, [
            'label' => 'Culture',
            'class' => Civilization::class,
            'remote_route' => 'civilization_typeahead',
            'allow_clear' => true,
            'multiple' => true,
            'attr' => [
                'add_path' => 'civilization_new',
                'add_label' => 'Add Civilization',
            ],
            'placeholder' => 'Search for an existing civilization by name',
        ]);
        $builder->add('civilizationOther', TextareaType::class, [
            'label' => 'Culture (unknown)',
            'required' => false,
            'help' => 'If the object\'s culture is unknown, explain why',
            'attr' => [
                'class' => 'tinymce',
            ],
        ]);
        $builder->add('displayYear', TextareaType::class, [
            'label' => 'Display Date',
            'required' => false,
            'help' => 'A textual description of the item\'s date, shown to the users. Blank for unknown.',
        ]);
        $builder->add('periodStart', EntityType::class, [
            'label' => 'Earliest creation',
            'class' => Period::class,
            'query_builder' => fn (EntityRepository $er) => $er->createQueryBuilder('p')->orderBy('p.sortableYear', 'ASC'),
            'choice_attr' => fn (Period $period, $key, $value) => ['data-year' => $period->getSortableYear()],
            'expanded' => false,
            'multiple' => false,
            'required' => false,
            'placeholder' => 'Unknown',
        ]);
        $builder->add('periodEnd', EntityType::class, [
            'label' => 'Latest creation',
            'class' => Period::class,
            'query_builder' => fn (EntityRepository $er) => $er->createQueryBuilder('p')->orderBy('p.sortableYear', 'ASC'),
            'choice_attr' => fn (Period $period, $key, $value) => ['data-year' => $period->getSortableYear()],
            'expanded' => false,
            'multiple' => false,
            'required' => false,
            'placeholder' => 'Unknown',
        ]);

        $builder->add('provenance', Select2EntityType::class, [
            'label' => 'Provenance',
            'class' => Location::class,
            'remote_route' => 'location_typeahead',
            'allow_clear' => true,
            'attr' => [
                'add_path' => 'location_new',
                'add_label' => 'Add Location',
            ],
            'placeholder' => 'Search for an existing location by name',
        ]);
        $builder->add('provenanceOther', TextareaType::class, [
            'label' => 'Provenance (unknown)',
            'required' => false,
            'help' => 'If the object\'s provenance is unknown, explain why',
            'attr' => [
                'class' => 'tinymce',
            ],
        ]);
        $builder->add('findspot', Select2EntityType::class, [
            'label' => 'Findspot',
            'class' => Location::class,
            'remote_route' => 'location_typeahead',
            'allow_clear' => true,
            'attr' => [
                'add_path' => 'location_new',
                'add_label' => 'Add Location',
            ],
            'placeholder' => 'Search for an existing location by name',
        ]);
        $builder->add('findspotOther', TextareaType::class, [
            'label' => 'Findspot (unknown)',
            'required' => false,
            'help' => 'If the object\'s findspot is unknown, explain why',
            'attr' => [
                'class' => 'tinymce',
            ],
        ]);
        $builder->add('dimensions', TextareaType::class, [
            'label' => 'Dimensions',
            'required' => false,
            'attr' => [
                'class' => 'tinymce',
            ],
        ]);
        $builder->add('materials', Select2EntityType::class, [
            'label' => 'Materials',
            'primary_key' => 'id',
            'text_property' => 'label',
            'multiple' => true,
            'required' => false,
            'remote_route' => 'material_typeahead',
            'class' => Material::class,
            'allow_clear' => true,
            'attr' => [
                'add_path' => 'material_new',
                'add_label' => 'Add Material',
            ],
            'placeholder' => 'Search for an existing material by name',
        ]);
        $builder->add('techniques', Select2EntityType::class, [
            'label' => 'Techniques',
            'primary_key' => 'id',
            'text_property' => 'label',
            'multiple' => true,
            'required' => false,
            'remote_route' => 'technique_typeahead',
            'class' => Technique::class,
            'allow_clear' => true,
            'attr' => [
                'add_path' => 'technique_new',
                'add_label' => 'Add Technique',
            ],
            'placeholder' => 'Search for an existing technique by name',
        ]);
        $builder->add('references', TextareaType::class, [
            'label' => 'Bibliographic References',
            'required' => false,
            'attr' => [
                'class' => 'tinymce',
            ],
        ]);
        $builder->add('location', TextareaType::class, [
            'label' => 'Location',
            'required' => false,
            'attr' => [
                'class' => 'tinymce',
            ],
        ]);
        $builder->add('subjects', Select2EntityType::class, [
            'label' => 'Subjects',
            'primary_key' => 'id',
            'text_property' => 'label',
            'multiple' => true,
            'required' => false,
            'remote_route' => 'subject_typeahead',
            'class' => Subject::class,
            'allow_clear' => true,
            'attr' => [
                'add_path' => 'subject_new',
                'add_label' => 'Add Subject',
            ],
            'placeholder' => 'Search for an existing subject by name',
        ]);
        $builder->add('contributions', CollectionType::class, [
            'label' => 'Contributors',
            'required' => true,
            'allow_add' => true,
            'allow_delete' => true,
            'delete_empty' => true,
            'entry_type' => ContributionType::class,
            'entry_options' => [
                'label' => false,
            ],
            'by_reference' => false,
            'attr' => [
                'class' => 'collection collection-complex',
            ],
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
            'data_class' => Item::class,
        ]);
    }
}
