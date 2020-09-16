<?php

namespace App\Command;

use App\Repository\ImageRepository;
use App\Services\Thumbnailer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ThumbnailsCommand extends Command
{
    protected static $defaultName = 'app:thumbnails';

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

    /**
     * @param EntityManagerInterface $em
     * @required
     */
    public function setDoctrine(EntityManagerInterface $em) {
        $this->em = $em;
    }

    /**
     * @param ImageRepository $repo
     * @required
     */
    public function setRepository(ImageRepository $repo) {
        $this->repo = $repo;
    }

    /**
     * @param Thumbnailer $thumbnailer
     * @required
     */
    public function setThumbnailer(Thumbnailer $thumbnailer) {
        $this->thumbnailer = $thumbnailer;
    }

    protected function configure()
    {
        $this
            ->setDescription('Generate thumbnails for the images.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $images = $this->repo->findAll();
        foreach($images as $image) {
            $output->writeln($image->getImagePath());
            $this->thumbnailer->thumbnail($image);
        }

        return 0;
    }
}
