<?php

declare(strict_types=1);

/*
 * (c) 2020 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\EventListener;

use App\Entity\Period;
use App\Repository\ItemRepository;
use Doctrine\ORM\Event\LifecycleEventArgs;

class PeriodListener {
    private ItemRepository $itemRepository;

    public function postLoad(LifecycleEventArgs $args) : void {
        $entity = $args->getEntity();
        if ( ! $entity instanceof Period) {
            return;
        }
        $entity->setItems($this->itemRepository->findItemsByPeriod($entity));
    }

    /**
     * @required
     */
    public function setItemRepository(ItemRepository $repository) : void {
        $this->itemRepository = $repository;
    }
}
