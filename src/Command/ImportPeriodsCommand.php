<?php

namespace App\Command;

use App\Entity\Period;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportPeriodsCommand extends Command
{
    protected static $defaultName = 'app:import:periods';
    /**
     * @var EntityManagerInterface
     */
    private $em;

    protected function configure()
    {
        $this->setDescription('Import periods from a CSV file.');
        $this->addArgument('file', InputArgument::REQUIRED, 'File to import');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $file = $input->getArgument('file');

        $csv = Reader::createFromPath($file);
        $csv->setHeaderOffset(0);
        foreach($csv->getRecords() as $index => $record) {
            $period = new Period();
            $period->setName($record['name']);
            $period->setLabel($record['label']);
            $period->setSortableYear($record['sortable year']);
            $this->em->persist($period);
        }
        $this->em->flush();

        return 0;
    }

    /**
     * @param EntityManagerInterface $em
     * @required
     */
    public function setEntityManager(EntityManagerInterface $em) {
        $this->em = $em;
    }
}
