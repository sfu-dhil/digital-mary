<?php


namespace App\EventListener;

use App\Entity\Period;
use App\Repository\ItemRepository;
use Doctrine\ORM\Event\LifecycleEventArgs;

class PeriodListener {

    /**
     * @var ItemRepository
     */
    private ItemRepository $itemRepository;

    public function postLoad(LifecycleEventArgs $args) {
        $entity = $args->getEntity();
        if( ! $entity instanceof Period) {
            return;
        }
        $entity->setItems($this->itemRepository->findItemsByPeriod($entity));
    }

    /**
     * @param ItemRepository $repository
     * @required
     */
    public function setItemRepository(ItemRepository $repository) {
        $this->itemRepository = $repository;
    }

}
