<?php

declare(strict_types=1);

namespace App\Services;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Description of FileUploader.
 *
 * @author Michael Joyce <ubermichael@gmail.com>
 */
class FileUploader {
    public const FORBIDDEN = '/[^a-z0-9_. -]/i';

    private ?string $uploadDir = null;

    public function __construct(private string $root) {}

    public function setUploadDir(string $dir) : void {
        if ('/' !== $dir[0]) {
            $this->uploadDir = $this->root . '/' . $dir;
        } else {
            $this->uploadDir = $dir;
        }
    }

    public function upload(UploadedFile $file) : string {
        $basename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $filename = implode('.', [
            preg_replace(self::FORBIDDEN, '_', $basename),
            uniqid(),
            $file->guessExtension(),
        ]);
        if ( ! file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0o777, true);
        }
        $file->move($this->uploadDir, $filename);

        return $filename;
    }

    /**
     * @return string
     */
    public function getUploadDir() {
        return $this->uploadDir;
    }

    /**
     * @param bool $asBytes
     *
     * @return float|int|string
     */
    public function getMaxUploadSize($asBytes = true) {
        static $maxBytes = -1;

        if ($maxBytes < 0) {
            $postMax = $this->parseSize(ini_get('post_max_size'));
            if ($postMax > 0) {
                $maxBytes = $postMax;
            }

            $uploadMax = $this->parseSize(ini_get('upload_max_filesize'));
            if ($uploadMax > 0 && $uploadMax < $maxBytes) {
                $maxBytes = $uploadMax;
            }
        }
        if ($asBytes) {
            return $maxBytes;
        }
        $units = ['b', 'Kb', 'Mb', 'Gb', 'Tb'];
        $exp = floor(log($maxBytes, 1024));
        $est = round($maxBytes / 1024 ** $exp, 1);

        return $est . $units[$exp];
    }

    /**
     * @param string $size
     */
    public function parseSize($size) : float {
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
        $bytes = (float) preg_replace('/[^\d\.]/', '', $size); // Remove the non-numeric characters from the size.
        if ($unit) {
            // Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
            return round($bytes * 1024 ** mb_stripos('bkmgtpezy', $unit[0]));
        }

        return round($bytes);
    }
}
