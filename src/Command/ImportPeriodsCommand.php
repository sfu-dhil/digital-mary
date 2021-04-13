<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Command;

use App\Entity\Period;
use Doctrine\ORM\EntityManagerInterface;
use League\Csv\Reader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportPeriodsCommand extends Command
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    protected static $defaultName = 'app:import:periods';

    protected function configure() : void {
        $this->setDescription('Import periods from a CSV file.');
        $this->addArgument('file', InputArgument::REQUIRED, 'File to import');
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int {
        $io = new SymfonyStyle($input, $output);
        $file = $input->getArgument('file');

        $csv = Reader::createFromPath($file);
        $csv->setHeaderOffset(0);

        foreach ($csv->getRecords() as $index => $record) {
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
     * @required
     */
    public function setEntityManager(EntityManagerInterface $em) : void {
        $this->em = $em;
    }
}
