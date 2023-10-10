<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Period;
use App\Repository\ItemRepository;
use Doctrine\ORM\Event\LifecycleEventArgs;

class PeriodListener {
    private ?ItemRepository $itemRepository = null;

    public function postLoad(LifecycleEventArgs $args) : void {
        $entity = $args->getEntity();
        if ( ! $entity instanceof Period) {
            return;
        }
        $entity->setItems($this->itemRepository->findItemsByPeriod($entity));
    }

    #[\Symfony\Contracts\Service\Attribute\Required]
    public function setItemRepository(ItemRepository $repository) : void {
        $this->itemRepository = $repository;
    }
}
