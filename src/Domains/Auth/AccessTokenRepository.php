<?php

declare(strict_types=1);

namespace LaminasApiSample\Domains\Auth;

use Doctrine\ORM\EntityRepository;
use Override;
use SensitiveParameter;

/** @extends EntityRepository<AccessTokenEntity> */
final class AccessTokenRepository extends EntityRepository implements
    AccessTokenLookupInterface
{
    #[Override]
    public function findByPlainToken(
        #[SensitiveParameter]
        string $token,
    ): ?AccessTokenEntity {
        return $this->findOneBy([
            'tokenHash' => AccessTokenEntity::hashToken($token),
        ]);
    }
}
