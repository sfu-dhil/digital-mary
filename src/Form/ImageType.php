<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Form;

use App\Entity\Image;
use App\Entity\Item;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Image form.
 */
class ImageType extends AbstractType {
    /**
     * Add form fields to $builder.
     */
    public function buildForm(FormBuilderInterface $builder, array $options) : void {
        $builder->add('item', EntityType::class, [
            'class' => Item::class,
            'disabled' => true,
        ]);
        $builder->add('public', ChoiceType::class, [
            'label' => 'Public',
            'expanded' => true,
            'multiple' => false,
            'required' => true,
            'choices' => [
                'No' => 0,
                'Yes' => 1,
            ],
            'attr' => [
                'help_block' => '',
            ],
        ]);
        $builder->add('description', null, [
            'label' => 'Description',
            'required' => false,
            'attr' => [
                'help_block' => '',
                'class' => 'tinymce',
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
            'data_class' => Image::class,
        ]);
    }
}
