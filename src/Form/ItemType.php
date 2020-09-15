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
use App\Entity\Location;
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
            'required' => false,
            'attr' => [
                'help_block' => '',
            ],
        ]);
        $builder->add('references', TextareaType::class, [
            'label' => 'Bibliography',
            'required' => false,
            'attr' => [
                'help_block' => '',
                'class' => 'tinymce',
            ],
        ]);

        $builder->add('circaDate', TextType::class, [
            'label' => 'Date',
            'attr' => [
                'help_block' => 'Gregorian date ranges (1901-1903) and circas (c1902) are supported here',
            ],
        ]);

        $builder->add('category', Select2EntityType::class, [
            'label' => 'Category',
            'class' => Category::class,
            'remote_route' => 'category_typeahead',
            'allow_clear' => true,
            'attr' => [
                'help_block' => '',
                'add_path' => 'category_new_popup',
                'add_label' => 'Add Category',
            ],
        ]);

        $builder->add('civilization', Select2EntityType::class, [
            'label' => 'Civilization',
            'class' => Civilization::class,
            'remote_route' => 'civilization_typeahead',
            'allow_clear' => true,
            'attr' => [
                'help_block' => '',
                'add_path' => 'civilization_new_popup',
                'add_label' => 'Add Civilization',
            ],
        ]);

        $builder->add('inscriptionStyle', Select2EntityType::class, [
            'label' => 'Inscription Style',
            'class' => InscriptionStyle::class,
            'remote_route' => 'inscription_style_typeahead',
            'allow_clear' => true,
            'attr' => [
                'help_block' => '',
                'add_path' => 'inscription_style_new_popup',
                'add_label' => 'Add InscriptionStyle',
            ],
        ]);

        $builder->add('findSpot', Select2EntityType::class, [
            'label' => 'Find Spot',
            'class' => Location::class,
            'remote_route' => 'location_typeahead',
            'allow_clear' => true,
            'attr' => [
                'help_block' => '',
                'add_path' => 'location_new_popup',
                'add_label' => 'Add Location',
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
