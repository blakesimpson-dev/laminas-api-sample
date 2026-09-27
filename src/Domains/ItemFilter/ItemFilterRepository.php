<?php

declare(strict_types=1);

namespace LaminasApiSample\Domains\ItemFilter;

use Doctrine\ORM\EntityRepository;
use LaminasApiSample\Domains\Profile\ProfileEntity;
use UnexpectedValueException;

/** @extends EntityRepository<ItemFilterEntity> */
final class ItemFilterRepository extends EntityRepository
{
    public function save(ItemFilterEntity $entity): void
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    /**
     * @return list<ItemFilterEntity>
     * @throws UnexpectedValueException
     */
    public function findAllByProfile(ProfileEntity $profile): array
    {
        return $this->findBy(['profile' => $profile]);
    }

    public function findOneByProfile(
        ProfileEntity $profile,
        string $id,
    ): ?ItemFilterEntity {
        return $this->findOneBy(['id' => $id, 'profile' => $profile]);
    }
}
