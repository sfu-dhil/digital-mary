<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\EventListener;

use App\Entity\Image;
use App\Services\FileUploader;
use App\Services\Thumbnailer;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Description of ClippingListener.
 *
 * @author Michael Joyce <ubermichael@gmail.com>
 */
class ImageListener {
    /**
     * @var FileUploader
     */
    private $uploader;

    /**
     * @var Thumbnailer
     */
    private $thumbnailer;

    /**
     * @var int
     */
    private $thumbWidth;

    /**
     * @var int
     */
    private $thumbHeight;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(FileUploader $uploader, Thumbnailer $thumbnailer, LoggerInterface $logger) {
        $this->uploader = $uploader;
        $this->thumbnailer = $thumbnailer;
        $this->logger = $logger;
    }

    private function uploadFile(Image $image) : void {
        $file = $image->getImageFile();
        if ( ! $file instanceof UploadedFile) {
            return;
        }
        $image->setOriginalName($file->getClientOriginalName());

        $filename = $this->uploader->upload($file);
        $path = $this->uploader->getUploadDir() . '/' . $filename;

        $dimensions = getimagesize($path);
        $image->setImageWidth($dimensions[0]);
        $image->setImageHeight($dimensions[1]);

        $clippingFile = new File($path);
        $image->setImageSize($clippingFile->getSize());
        $image->setImageFile($clippingFile);
        $image->setImagePath($filename);
        $image->setThumbPath($this->thumbnailer->thumbnail($image));
    }

    public function setThumbWidth($width) : void {
        $this->thumbWidth = $width;
    }

    public function setThumbHeight($height) : void {
        $this->thumbHeight = $height;
    }

    public function prePersist(LifecycleEventArgs $args) : void {
        $entity = $args->getEntity();
        if ($entity instanceof Image) {
            $this->uploadFile($entity);
        }
    }

    public function preUpdate(LifecycleEventArgs $args) : void {
        $entity = $args->getEntity();
        if ($entity instanceof Image) {
            $this->uploadFile($entity);
        }
    }

    public function postLoad(LifecycleEventArgs $args) : void {
        $entity = $args->getEntity();
        if ($entity instanceof Image) {
            $filePath = $this->uploader->getUploadDir() . '/' . $entity->getImagePath();
            $thumbnailPath = $this->uploader->getUploadDir() . '/' . $entity->getThumbPath();
            if (file_exists($filePath)) {
                $entity->setImageFile(new File($filePath));
            }
            if (file_exists($thumbnailPath)) {
                $entity->setThumbFile(new File($thumbnailPath));
            }
        }
    }
}
