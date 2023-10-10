<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Image;
use App\Entity\Item;
use App\Services\FileUploader;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Image form.
 */
class ImageType extends AbstractType {
    public function __construct(private FileUploader $fileUploader) {}

    /**
     * Add form fields to $builder.
     */
    public function buildForm(FormBuilderInterface $builder, array $options) : void {
        $builder->add('item', EntityType::class, [
            'class' => Item::class,
            'disabled' => true,
        ]);
        $builder->add('imageFile', FileType::class, [
            'label' => 'Image',
            'required' => true,
            'help' => "Select a file to upload which is less than {$this->fileUploader->getMaxUploadSize(false)} in size.",
            'attr' => [
                'data-maxsize' => $this->fileUploader->getMaxUploadSize(),
            ],
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
        ]);
        $builder->add('description', TextareaType::class, [
            'label' => 'Description',
            'required' => false,
            'attr' => [
                'class' => 'tinymce',
            ],
        ]);
        $builder->add('license', TextareaType::class, [
            'label' => 'License',
            'required' => false,
            'attr' => [
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
