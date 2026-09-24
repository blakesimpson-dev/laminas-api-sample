<?php

declare(strict_types=1);

namespace LaminasApiSample\Domains\ItemFilter;

use Doctrine\ORM\EntityRepository;

/** @extends EntityRepository<ItemFilterEntity> */
final class ItemFilterRepository extends EntityRepository
{
    public function save(ItemFilterEntity $entity): void
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }
}
