<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Command;

use App\Repository\ImageRepository;
use App\Services\Thumbnailer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ThumbnailsCommand extends Command {
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var ImageRepository
     */
    private $repo;

    /**
     * @var Thumbnailer
     */
    private $thumbnailer;
    protected static $defaultName = 'app:thumbnails';

    protected function configure() : void {
        $this
            ->setDescription('Generate thumbnails for the images.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int {
        $images = $this->repo->findAll();
        foreach ($images as $image) {
            $output->writeln($image->getImagePath());
            $this->thumbnailer->thumbnail($image);
        }

        return 0;
    }

    /**
     * @required
     */
    public function setDoctrine(EntityManagerInterface $em) : void {
        $this->em = $em;
    }

    /**
     * @required
     */
    public function setRepository(ImageRepository $repo) : void {
        $this->repo = $repo;
    }

    /**
     * @required
     */
    public function setThumbnailer(Thumbnailer $thumbnailer) : void {
        $this->thumbnailer = $thumbnailer;
    }
}
