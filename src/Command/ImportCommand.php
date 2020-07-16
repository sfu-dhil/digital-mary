<?php

namespace App\Command;

use App\Entity\Category;
use App\Entity\Image;
use App\Entity\Item;
use App\Entity\Material;
use App\Entity\Subject;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Nines\UtilBundle\Services\Text;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImportCommand extends Command
{
    protected static $defaultName = 'app:import';

    /**
     * @var Connection
     */
    private $source;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var array
     */
    private $idMapping;

    /**
     * @var Text
     */
    private $text;

    public function __construct(Connection $oldEm, EntityManagerInterface $em, Text $text) {
        parent::__construct();
        $this->source = $oldEm;
        $this->em = $em;
        $this->idMapping = [];
        $this->text = $text;
    }

    protected function configure()
    {
        $this->setDescription('Import from omeka');
    }

    /**
     * @param string $class
     * @param int $old
     * @param int $new
     */
    protected function setIdMap($class, $old, $new) : void {
        $this->idMapping[$class][$old] = $new;
    }

    /**
     * @param string $class
     * @param int $old
     * @param int $default
     *
     * @return null|int
     */
    protected function getIdMap($class, $old, $default = null) {
        if (isset($this->idMapping[$class][$old])) {
            return $this->idMapping[$class][$old];
        }

        return $default;
    }

    /**
     * @param string $class
     * @param int $oldId
     * @param mixed $default
     */
    public function findEntity($class, $oldId, $default = null) {
        $newId = $this->getIdMap($class, $oldId);
        if ( ! $newId) {
            return $default;
        }

        return $this->em->find($class, $newId);
    }

    public function findTerm($class, $label) {
        $label = trim($label);
        $repo = $this->em->getRepository($class);
        $term = $repo->findOneBy(array('label' => $label));
        if( ! $term) {
            $term = new $class();
            $term->setLabel($label);
            $term->setName($this->text->slug($label));
            $this->em->persist($term);
            $this->em->flush();
        }
        return $term;
    }

    protected function items()
    {
        $itemQuery = $this->source->query("SELECT * FROM omeka_items");
        $dcStmt = $this->source->prepare("SELECT * FROM omeka_element_texts WHERE record_id = :id");
        while($itemRow = $itemQuery->fetch()) {
            $item = new Item();
            $dcStmt->execute(['id' => $itemRow['id']]);
            while($dcRow = $dcStmt->fetch()) {
                switch($dcRow['element_id']) {
                    case 37:
                        // contributor initials
                        if($d = date_parse($itemRow['modified'])) {
                            $item->addRevision("{$d['year']}-{$d['month']}-{$d['day']}", $dcRow['text']);
                        }
                        break;
                    case 40:
                        // date '6th Centure, unknown, Post-6th Century'
                        $m = [];
                        if(preg_match('/(\d+)th/', $dcRow['text'], $m)) {
                            if(is_int($m[0])) {
                                $item->setCircaDate('c' . ($m[1] - 1) . '00');
                            }
                        }
                        break;
                    case 41:
                        // description with html
                        $item->setDescription($dcRow['text']);
                        break;
                    case 44:
                        // language. "greek"
                        break;
                    case 49:
                        // subject
                        $subject = $this->findTerm(Subject::class, $dcRow['text']);
                        $item->addSubject($subject);
                        break;
                    case 50:
                        // name/title
                        $item->setName($dcRow['text']);
                        break;
                    case 51:
                        // Label of category
                        $category = $this->findTerm(Category::class, $dcRow['text']);
                        $item->setCategory($category);
                        break;
                    case 79:
                        $material = $this->findTerm(Material::class, $dcRow['text']);
                        $item->addMaterial($material);
                        break;
                    case 80:
                        $item->setReferences($dcRow['text']);
                        break;
                    case 81:
                        // description of the thing in physical context (niche wall of chapel 3)
                        $item->setDescription($item->getDescription() . "<p>{$dcRow['text']}</p>");
                        break;
                }
            }
            $this->em->persist($item);
            $this->em->flush();
            $this->setIdMap(Item::class, $itemRow['id'], $item->getId());
        }
    }

    public function images() {
        $fileQuery = $this->source->query("SELECT item_id, mime_type, filename FROM omeka_files");
        while($fileRow = $fileQuery->fetch()) {
            $image = new Image();
            $image->setPublic(true);
            $item = $this->em->find(Item::class, $this->getIdMap(Item::class, $fileRow['item_id']));
            $image->setItem($item);
            $upload = new UploadedFile('/Users/michael/Sites/dm/files/original/' . $fileRow['filename'], $fileRow['filename'], $fileRow['mime_type'], null, true);
            $image->setImageFile($upload);
            $this->em->persist($image);
            $this->em->flush();
        }
    }

    public function execute(InputInterface $input, OutputInterface $output) : int {
        $this->items();
        $this->images();
        return 0;
    }
}
