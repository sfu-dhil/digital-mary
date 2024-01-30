<?php

declare(strict_types=1);

namespace App\Command;

use App\Config\ContributorRole;
use App\Entity\Contribution;
use App\Entity\Item;
use App\Entity\Person;
use App\Repository\ItemRepository;
use App\Repository\PersonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

// NOTE: This should ideally only be run one time
#[AsCommand(name: 'app:migrate:item_contributions')]
class MigrateItemContributionsCommand extends Command {
    public function __construct(
        private EntityManagerInterface $em,
        private ItemRepository $itemRepository,
        private PersonRepository $personRepository,
        private array $personFullNameLookUp = [],
    ) {
        parent::__construct();
    }

    private function getOrCreatePerson(string $fullName) : Person {
        if ( ! array_key_exists($fullName, $this->personFullNameLookUp)) {
            $person = new Person();
            $person->setFullname($fullName);
            $person->setCitationName($this->hackySimpleCitationName($fullName));

            $this->em->persist($person);
            $this->em->flush();
            $this->personFullNameLookUp[$fullName] = $person;
        }

        return $this->personFullNameLookUp[$fullName];
    }

    private function hackySimpleCitationName(string $fullName) : string {
        $nameParts = explode(' ', $fullName);
        if (2 === count($nameParts)) {
            return "{$nameParts[1]}, {$nameParts[0]}";
        }

        return $fullName;
    }

    private function addContributorRole(Item $item, array &$personContributionLookup, string $fullName, ContributorRole $role) : void {
        $person = $this->getOrCreatePerson($fullName);

        $contribution = $personContributionLookup[$person->getId()] ?? null;
        if ( ! $contribution) {
            $contribution = new Contribution();
            $contribution->setPerson($person);
            $contribution->setItem($item);
            $personContributionLookup[$person->getId()] = $contribution;
        }

        $roles = $contribution->getRoles();
        if ( ! in_array($role, $roles, true)) {
            $roles[] = $role;
            $contribution->setRoles($roles);

            $this->em->persist($contribution);
            $this->em->flush();
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int {
        $this->personFullNameLookUp = [];

        // populate initial personFullNameLookUp
        foreach ($this->personRepository->findAll() as $person) {
            $this->personFullNameLookUp[$person->getFullname()] = $person;
        }

        foreach ($this->itemRepository->findAll() as $item) {
            $personContributionLookup = [];
            foreach ($item->getContributions() as $contribution) {
                $personContributionLookup[$contribution->getPerson()->getId()] = $contribution;
            }

            // add authors
            foreach ($item->getRevisions() as $revision) {
                $name = $revision['initials'] ?? '';
                // not using just 2 letter initials
                if (mb_strlen($name) > 2) {
                    $this->addContributorRole($item, $personContributionLookup, $name, ContributorRole::aut);
                }
            }

            // add data manager
            $this->addContributorRole($item, $personContributionLookup, 'Sabrina Higgins', ContributorRole::dtm);
        }

        return 1;
    }
}
