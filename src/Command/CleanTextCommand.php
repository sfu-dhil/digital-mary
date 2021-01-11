<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Command;

use App\Entity\Image;
use App\Entity\Item;
use Doctrine\ORM\EntityManagerInterface;
use Normalizer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CleanTextCommand extends Command {
    public const ALLOWED = '<a><b><blockquote><br><dd><del><dl><dt><em><h1><h2><h3><h4><h5><h6><hr><i><img><ins><kbd><li><mark><ol><p><pre><q><s><small><strong><sub><sup><u><ul>';

    /**
     * @var EntityManagerInterface
     */
    private $em;

    protected static $defaultName = 'app:clean:text';

    protected function configure() : void {
        $this->setDescription('Clean up the HTML tags in text fields.');
    }

    /**
     * @param string $string
     */
    protected function clean(?string $string) {
        if ( ! $string) {
            return null;
        }
        // Remove HTML
        $s = strip_tags($string, self::ALLOWED);

        // Decode entities
        $s = html_entity_decode($s, ENT_QUOTES | ENT_SUBSTITUTE | ENT_DISALLOWED | ENT_HTML5, 'UTF-8');

        // Normalize unicode
        $s = Normalizer::normalize($s, Normalizer::NFC);

        // Normalize the line endings
        $s = str_replace(["\r\n", "\r", "\n"], "\n", $s);

        // Remove excess line endings
        $s = preg_replace("/\n{3,}/", "\n\n", $s);

        // Replace whitespace that is not a new line with a space
        $s = preg_replace('/[^\S\n]+/u', ' ', $s);

        // Trim whitespace at the start and end
        return preg_replace('/^\s+|\s+$/u', '', $s);
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int {
        foreach ($this->em->getRepository(Image::class)->findAll() as $image) {
            $image->setDescription($this->clean($image->getDescription()));
            $image->setLicense($this->clean($image->getLicense()));
            $this->em->flush();
        }

        foreach ($this->em->getRepository(Item::class)->findAll() as $item) {
            // @var Item $item
            $item->setDescription($this->clean($item->getDescription()));
            $item->setInscription($this->clean($item->getInscription()));
            $item->setTranslatedInscription($this->clean($item->getTranslatedInscription()));
            $item->setDimensions($this->clean($item->getDimensions()));
            $item->setReferences($this->clean($item->getReferences()));
            $item->setCivilizationOther($this->clean($item->getCivilizationOther()));
            $item->setFindspotOther($this->clean($item->getFindspotOther()));
            $item->setProvenanceOther($this->clean($item->getProvenanceOther()));
            $item->setLocation($this->clean($item->getLocation()));
            $this->em->flush();
        }

        // image: description, license
        // item: description, inscription, translated inscription, dimensions, bibliography,
        //       civilization_other, findspot_other, provenance_other, location

        return 0;
    }

    /**
     * @required
     */
    public function setEntityManager(EntityManagerInterface $em) : void {
        $this->em = $em;
    }
}
