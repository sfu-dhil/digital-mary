<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Form;

use App\Entity\Category;
use App\Entity\Civilization;
use App\Entity\InscriptionStyle;
use App\Entity\Item;
use App\Entity\Language;
use App\Entity\Location;
use App\Entity\Material;
use App\Entity\Subject;
use App\Entity\Technique;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
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
            'attr' => [
                'help_block' => '',
            ],
        ]);
        $builder->add('category', Select2EntityType::class, [
            'label' => 'Category',
            'class' => Category::class,
            'remote_route' => 'category_typeahead',
            'allow_clear' => true,
            'multiple' => true,
            'attr' => [
                'help_block' => '',
                'add_path' => 'category_new_popup',
                'add_label' => 'Add Category',
            ],
        ]);
        $builder->add('description', TextareaType::class, [
            'label' => 'Description',
            'required' => false,
            'attr' => [
                'help_block' => '',
                'class' => 'tinymce',
            ],
        ]);
        $builder->add('inscription', TextareaType::class, [
            'label' => 'Inscription',
            'required' => false,
            'attr' => [
                'help_block' => '',
                'class' => 'tinymce',
            ],
        ]);
        $builder->add('translatedInscription', TextareaType::class, [
            'label' => 'Inscription Translation',
            'required' => false,
            'attr' => [
                'help_block' => '',
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
                'help_block' => '',
                'add_path' => 'language_new_popup',
                'add_label' => 'Add Language',
            ],
        ]);
        $builder->add('inscriptionStyle', Select2EntityType::class, [
            'label' => 'Inscription Type',
            'class' => InscriptionStyle::class,
            'remote_route' => 'inscription_style_typeahead',
            'allow_clear' => true,
            'attr' => [
                'help_block' => '',
                'add_path' => 'inscription_style_new_popup',
                'add_label' => 'Add Inscr. Type',
            ],
        ]);
        $builder->add('civilization', Select2EntityType::class, [
            'label' => 'Culture',
            'class' => Civilization::class,
            'remote_route' => 'civilization_typeahead',
            'allow_clear' => true,
            'multiple' => true,
            'attr' => [
                'help_block' => '',
                'add_path' => 'civilization_new_popup',
                'add_label' => 'Add Civilization',
            ],
        ]);
        $builder->add('civilizationOther', TextareaType::class, [
            'label' => 'Culture (unknown)',
            'required' => false,
            'attr' => [
                'help_block' => 'If the object\'s culture is unknown, explain why',
                'class' => 'tinymce',
            ],
        ]);
        $builder->add('displayYear', TextareaType::class, [
            'label' => 'Display Date',
            'required' => false,
            'attr' => [
                'help_block' => 'A textual description of the item\'s date, shown to the users. Blank for unknown.',
            ],
        ]);
        $builder->add('gregorianYear', IntegerType::class, [
            'label' => 'Gregorian Date',
            'required' => false,
            'attr' => [
                'help_block' => 'A number representing the most accurately known date of creation for the object, used only for sorting and searching',
            ],
        ]);
        $builder->add('provenance', Select2EntityType::class, [
            'label' => 'Provenance',
            'class' => Location::class,
            'remote_route' => 'location_typeahead',
            'allow_clear' => true,
            'attr' => [
                'help_block' => '',
                'add_path' => 'location_new_popup',
                'add_label' => 'Add Location',
            ],
        ]);
        $builder->add('provenanceOther', TextareaType::class, [
            'label' => 'Provenance (unknown)',
            'required' => false,
            'attr' => [
                'help_block' => 'If the object\'s provenance is unknown, explain why',
                'class' => 'tinymce',
            ],
        ]);
        $builder->add('findspot', Select2EntityType::class, [
            'label' => 'Findspot',
            'class' => Location::class,
            'remote_route' => 'location_typeahead',
            'allow_clear' => true,
            'attr' => [
                'help_block' => '',
                'add_path' => 'location_new_popup',
                'add_label' => 'Add Location',
            ],
        ]);
        $builder->add('findspotOther', TextareaType::class, [
            'label' => 'Findspot (unknown)',
            'required' => false,
            'attr' => [
                'help_block' => 'If the object\'s findspot is unknown, explain why',
                'class' => 'tinymce',
            ],
        ]);
        $builder->add('dimensions', TextareaType::class, [
            'label' => 'Dimensions',
            'required' => false,
            'attr' => [
                'help_block' => '',
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
                'help_block' => '',
                'add_path' => 'material_new_popup',
                'add_label' => 'Add Material',
            ],
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
                'help_block' => '',
                'add_path' => 'technique_new_popup',
                'add_label' => 'Add Technique',
            ],
        ]);
        $builder->add('references', TextareaType::class, [
            'label' => 'Bibliographic References',
            'required' => false,
            'attr' => [
                'help_block' => '',
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
                'help_block' => '',
                'add_path' => 'subject_new_popup',
                'add_label' => 'Add Subject',
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
