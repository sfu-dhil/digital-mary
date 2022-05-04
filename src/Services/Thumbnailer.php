<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Services;

use App\Entity\Image;
use Exception;
use Imagick;
use ImagickException;
use ImagickPixel;
use Psr\Log\LoggerInterface;

/**
 * Description of Thumbnailer.
 *
 * @author mjoyce
 */
class Thumbnailer {
    private ?int $width = null;

    private ?int $height = null;

    public function setWidth(int $width) : void {
        $this->width = $width;
    }

    public function setHeight(int $height) : void {
        $this->height = $height;
    }

    /**
     * @return string
     * @throws ImagickException
     * @throws Exception
     */
    public function thumbnail(Image $image) {
        $file = $image->getImageFile();
        $thumbname = $file->getBasename('.' . $file->getExtension()) . '_tn.png';

        $magick = new Imagick($file->getPathname());
        $magick->setBackgroundColor(new ImagickPixel('white'));
        $magick->thumbnailimage($this->width, $this->height, true, false);
        $magick->setImageFormat('png32');
        $path = $file->getPath() . '/' . $thumbname;

        $handle = fopen($path, 'wb');
        if ( ! $handle) {
            $error = error_get_last();

            throw new Exception("Cannot open {$path} for write. " . $error['message']);
        }
        fwrite($handle, $magick->getimageblob());

        return $thumbname;
    }
}
