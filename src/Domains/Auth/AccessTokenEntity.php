<?php

declare(strict_types=1);

namespace LaminasApiSample\Domains\Auth;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types as DoctrineDBTypes;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use InvalidArgumentException;
use LaminasApiSample\Domains\Profile\ProfileEntity;
use LaminasApiSample\Domains\TimestampedEntity;
use Ramsey\Uuid\Uuid;
use SensitiveParameter;

#[Entity(repositoryClass: AccessTokenRepository::class)]
#[Table(name: 'access_token')]
final class AccessTokenEntity extends TimestampedEntity
{
    private const string HASH_ALGO = 'sha256';

    #[ManyToOne(targetEntity: ProfileEntity::class)]
    #[JoinColumn(
        name: 'profile_id',
        referencedColumnName: 'uuid',
        nullable: false,
    )]
    private readonly ProfileEntity $profile;

    #[Column(type: DoctrineDBTypes::GUID), Id]
    private readonly string $id;

    // @mago-expect analysis:write-only-property
    #[Column(name: 'token_hash', length: 64, unique: true)]
    private readonly string $tokenHash;

    /** @var list<string> $scopes */
    #[Column(type: DoctrineDBTypes::JSON)]
    private readonly array $scopes;

    #[Column(name: 'expires_at', type: DoctrineDBTypes::DATETIMETZ_IMMUTABLE)]
    private readonly DateTimeImmutable $expiresAt;

    #[Column(
        name: 'revoked_at',
        type: DoctrineDBTypes::DATETIMETZ_IMMUTABLE,
        nullable: true,
    )]
    private ?DateTimeImmutable $revokedAt = null;

    public static function hashToken(
        #[SensitiveParameter]
        string $plainToken,
    ): string {
        return hash(self::HASH_ALGO, $plainToken);
    }

    /**
     * @param list<string> $scopes
     * @mago-expect lint:excessive-parameter-list
     */
    public function __construct(
        DateTimeImmutable $createdAt,
        ProfileEntity $profile,
        #[SensitiveParameter]
        string $plainToken,
        array $scopes,
        DateTimeImmutable $expiresAt,
        ?DateTimeImmutable $revokedAt = null,
    ) {
        if ($plainToken === '') {
            throw new InvalidArgumentException(
                'Access token must not be empty.',
            );
        }

        parent::__construct($createdAt);
        $this->id = Uuid::uuid4()->toString();
        $this->profile = $profile;
        $this->tokenHash = self::hashToken($plainToken);
        $this->scopes = $scopes;
        $this->expiresAt = $expiresAt;
        $this->revokedAt = $revokedAt;
    }

    public function hasScope(string $scope): bool
    {
        return in_array($scope, $this->scopes, true);
    }

    public function isActive(DateTimeImmutable $now): bool
    {
        return $this->revokedAt === null && $now < $this->expiresAt;
    }

    public function revoke(DateTimeImmutable $now): void
    {
        $this->revokedAt ??= $now;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getProfile(): ProfileEntity
    {
        return $this->profile;
    }

    /** @return list<string> */
    public function getScopes(): array
    {
        return $this->scopes;
    }
}
