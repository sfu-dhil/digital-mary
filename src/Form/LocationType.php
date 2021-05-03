<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Form;

use App\Entity\Location;
use Nines\UtilBundle\Form\TermType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Location form.
 */
class LocationType extends TermType {
    /**
     * Add form fields to $builder.
     */
    public function buildForm(FormBuilderInterface $builder, array $options) : void {
        parent::buildForm($builder, $options);

        $builder->add('latitude', NumberType::class, [
            'label' => 'Latitude',
            'html5' => true,
            'input' => 'number',
            'scale' => 8,
            'required' => false,
            'attr' => [
                'help_block' => '',
                'step' => 'any',
            ],
        ]);
        $builder->add('longitude', NumberType::class, [
            'label' => 'Longitude',
            'html5' => true,
            'input' => 'number',
            'scale' => 8,
            'required' => false,
            'attr' => [
                'help_block' => '',
                'step' => 'any',
            ],
        ]);
        $builder->add('country', TextType::class, [
            'label' => 'Country',
            'required' => false,
            'attr' => [
                'help_block' => '',
            ],
        ]);
        $builder->add('alternateNames', CollectionType::class, [
            'label' => 'Alternate Names',
            'required' => false,
            'allow_add' => true,
            'allow_delete' => true,
            'delete_empty' => true,
            'entry_type' => TextType::class,
            'entry_options' => [
                'label' => false,
            ],
            'by_reference' => false,
            'attr' => [
                'class' => 'collection collection-simple',
                'help_block' => '',
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
            'data_class' => Location::class,
        ]);
    }
}
