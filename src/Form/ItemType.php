<?php

namespace App\Form;

use App\Entity\Item;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

/**
 * Item form.
 */
class ItemType extends AbstractType {

    /**
     * Add form fields to $builder.
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
                                    $builder->add('name', TextType::class, array(
                    'label' => 'Name',
                    'required' => true,
                    'attr' => array(
                        'help_block' => '',
                    ),
                ));
                                        $builder->add('description', TextareaType::class, array(
                    'label' => 'Description',
                    'required' => false,
                    'attr' => array(
                        'help_block' => '',
                        'class' => 'tinymce',
                    ),
                ));
                                        $builder->add('inscription', TextareaType::class, array(
                    'label' => 'Inscription',
                    'required' => false,
                    'attr' => array(
                        'help_block' => '',
                        'class' => 'tinymce',
                    ),
                ));
                                        $builder->add('translatedInscription', TextareaType::class, array(
                    'label' => 'Translated Inscription',
                    'required' => false,
                    'attr' => array(
                        'help_block' => '',
                        'class' => 'tinymce',
                    ),
                ));
                                        $builder->add('dimensions', TextType::class, array(
                    'label' => 'Dimensions',
                    'required' => true,
                    'attr' => array(
                        'help_block' => '',
                    ),
                ));
                                        $builder->add('references', TextareaType::class, array(
                    'label' => 'References',
                    'required' => false,
                    'attr' => array(
                        'help_block' => '',
                        'class' => 'tinymce',
                    ),
                ));
                                        $builder->add('revisions', null, [
                    'label' => 'Revisions',
                    'required' => true,
                    'attr' => array(
                        'help_block' => '',
                    ),
                ]);
            
    }

    /**
     * Define options for the form.
     *
     * Set default, optional, and required options passed to the
     * buildForm() method via the $options parameter.
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Item::class
        ));
    }

}
