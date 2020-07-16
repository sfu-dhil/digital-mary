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
use App\Entity\Epigraphy;
use App\Entity\Item;
use App\Entity\Location;
use App\Entity\Material;
use App\Entity\Subject;
use App\Entity\Technique;
use Symfony\Component\Form\AbstractType;
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
        $builder->add('description', TextareaType::class, [
            'label' => 'Description',
            'required' => false,
            'attr' => [
                'help_block' => '',
                'class' => 'tinymce',
            ],
        ]);
        $builder->add('circaDate', TextareaType::class, [
            'label' => 'Date',
            'required' => false,
            'attr' => [
                'help_block' => 'Date ranges (1901-1903) and circas (c1902) are supported here',
            ]
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
            'label' => 'Translated Inscription',
            'required' => false,
            'attr' => [
                'help_block' => '',
                'class' => 'tinymce',
            ],
        ]);
        $builder->add('dimensions', TextType::class, [
            'label' => 'Dimensions',
            'required' => true,
            'attr' => [
                'help_block' => '',
            ],
        ]);
        $builder->add('references', TextareaType::class, [
            'label' => 'References',
            'required' => false,
            'attr' => [
                'help_block' => '',
                'class' => 'tinymce',
            ],
        ]);
        $builder->add('category', Select2EntityType::class, [
            'label' => 'Category',
            'multiple' => false,
            'remote_route' => 'category_typeahead',
            'class' => Category::class,
            'allow_clear' => true,
            'attr' => [
                'add_path' => 'category_new_popup',
                'add_label' => 'New Category',
                'help_block' => '',
            ]
        ]);
        $builder->add('civilization', Select2EntityType::class, [
            'label' => 'Civilization',
            'multiple' => false,
            'remote_route' => 'civilization_typeahead',
            'class' => Civilization::class,
            'allow_clear' => true,
            'attr' => [
                'add_path' => 'civilization_new_popup',
                'add_label' => 'New Civilization',
                'help_block' => '',
            ]
        ]);
        $builder->add('epigraphy', Select2EntityType::class, [
            'label' => 'Epigraphy',
            'multiple' => false,
            'remote_route' => 'epigraphy_typeahead',
            'class' => Epigraphy::class,
            'allow_clear' => true,
            'attr' => [
                'add_path' => 'epigraphy_new_popup',
                'add_label' => 'New Epigraphy',
                'help_block' => '',
            ]
        ]);
        $builder->add('provenance', Select2EntityType::class, [
            'label' => 'Provenance',
            'multiple' => false,
            'remote_route' => 'location_typeahead',
            'class' => Location::class,
            'allow_clear' => true,
            'attr' => [
                'add_path' => 'location_new_popup',
                'add_label' => 'New Location',
                'help_block' => '',
            ]
        ]);
        $builder->add('findSpot', Select2EntityType::class, [
            'label' => 'Find Spot',
            'multiple' => false,
            'remote_route' => 'location_typeahead',
            'class' => Location::class,
            'allow_clear' => true,
            'attr' => [
                'add_path' => 'location_new_popup',
                'add_label' => 'New Location',
                'help_block' => '',
            ]
        ]);
        $builder->add('materials', Select2EntityType::class, [
            'label' => 'Materials',
            'multiple' => true,
            'remote_route' => 'material_typeahead',
            'class' => Material::class,
            'allow_clear' => true,
            'attr' => [
                'add_path' => 'material_new_popup',
                'add_label' => 'New Material',
                'help_block' => '',
            ]
        ]);

        $builder->add('techniques', Select2EntityType::class, [
            'label' => 'Techniques',
            'multiple' => true,
            'remote_route' => 'category_typeahead',
            'class' => Technique::class,
            'allow_clear' => true,
            'attr' => [
                'add_path' => 'technique_new_popup',
                'add_label' => 'New Technique',
                'help_block' => '',
            ]
        ]);
        $builder->add('subjects', Select2EntityType::class, [
            'label' => 'Subjects',
            'multiple' => true,
            'remote_route' => 'subject_typeahead',
            'class' => Subject::class,
            'allow_clear' => true,
            'attr' => [
                'add_path' => 'subject_new_popup',
                'add_label' => 'New Subject',
                'help_block' => '',
            ]
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
